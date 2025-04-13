<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\CartItem;
use App\Traits\ApiResponse;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $user = Auth::user();
            $cartItems = $user->cartItems()->with(['product' => function($query) {
                $query->with('media');
            }])->get();

            $items = $cartItems->map(function ($item) {
                return $this->formatCartItem($item);
            });

            $subtotal = $items->sum('item_total');
            $totalItems = $items->sum('quantity');

            return $this->successResponse([
                'items' => $items,
                'summary' => [
                    'total_items' => $totalItems,
                    'subtotal' => $subtotal,
                    'shipping' => 0,
                    'total' => $subtotal
                ]
            ], 'Cart items retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve cart items: ' . $e->getMessage(), 500);
        }
    }

    public function addToCart(AddToCartRequest $request)
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);

            $this->validateQuantity($product, $request->quantity);

            $existingItem = $user->cartItems()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;
                $this->validateQuantity($product, $newQuantity, $existingItem->quantity);
                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                $user->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price
                ]);
            }

            return redirect()->route('cart.index');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add item to cart: ' . $e->getMessage(), 500);
        }
    }

    public function updateCartItem(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        try {
            $this->authorizeCartItem($cartItem);

            $product = $cartItem->product;
            $this->validateQuantity($product, $request->quantity);

            $cartItem->update(['quantity' => $request->quantity]);

            return redirect()->route('cart.index');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update cart item: ' . $e->getMessage(), 500);
        }
    }

    public function removeFromCart(CartItem $cartItem)
    {
        try {
            $this->authorizeCartItem($cartItem);
            $cartItem->delete();
            return redirect()->route('cart.index');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove item from cart: ' . $e->getMessage(), 500);
        }
    }

    public function clearCart()
    {
        try {
            Auth::user()->cartItems()->delete();

            return $this->successResponse([
                'items' => [],
                'summary' => [
                    'total_items' => 0,
                    'subtotal' => 0,
                    'total' => 0
                ]
            ], 'Cart cleared successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear cart: ' . $e->getMessage(), 500);
        }
    }

    private function formatCartItem(CartItem $item)
    {
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'name' => $item->product->name,
            'description' => $item->product->description,
            'price' => $item->price,
            'quantity' => $item->quantity,
            'image' => $item->product->featured_image?->url,
            'max_quantity' => $item->product->quantity,
            'item_total' => $item->price * $item->quantity
        ];
    }

    private function authorizeCartItem(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
    }

    private function validateQuantity(Product $product, $quantity, $currentQuantity = 0)
    {
        if ($quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Only ' . $product->quantity . ' items available in stock'
            ]);
        }

        if ($currentQuantity > 0 && $quantity > ($product->quantity - $currentQuantity)) {
            throw ValidationException::withMessages([
                'quantity' => 'You already have ' . $currentQuantity .
                              ' in cart. Only ' . ($product->quantity - $currentQuantity) .
                              ' more available.'
            ]);
        }
    }
}
