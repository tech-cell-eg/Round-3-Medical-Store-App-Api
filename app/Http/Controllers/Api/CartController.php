<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with(['product' => function($query) {
            $query->with('media');
        }])->get();

        $items = $cartItems->map(function ($item) {
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
        });

        $subtotal = $items->sum('item_total');
        $totalItems = $items->sum('quantity');

        return response()->json([
            'items' => $items,
            'summary' => [
                'total_items' => $totalItems,
                'subtotal' => $subtotal,
                'shipping' => 0,
                'total' => $subtotal
            ]
        ]);
    }

    public function addToCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::user();
            $product = Product::findOrFail($validated['product_id']);

            if ($product->quantity < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only ' . $product->quantity . ' items available in stock'
                ]);
            }

            $existingItem = $user->cartItems()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $validated['quantity'];

                if ($newQuantity > $product->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'You already have ' . $existingItem->quantity .
                                      ' in cart. Only ' . ($product->quantity - $existingItem->quantity) .
                                      ' more available.'
                    ]);
                }

                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                $user->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'price' => $product->price
                ]);
            }

            return $this->index();

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updateCartItem(Request $request, CartItem $cartItem)
    {
        try {
            if ($cartItem->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action');
            }

            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $product = $cartItem->product;

            if ($validated['quantity'] > $product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only ' . $product->quantity . ' items available in stock'
                ]);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            return $this->index();

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function removeFromCart(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $cartItem->delete();

        return $this->index();
    }

    public function clearCart()
    {
        Auth::user()->cartItems()->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
            'data' => [
                'items' => [],
                'summary' => [
                    'total_items' => 0,
                    'subtotal' => 0,
                    'total' => 0
                ]
            ]
        ]);
    }
}
