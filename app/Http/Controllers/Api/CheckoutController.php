<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Traits\ApiResponse;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function summary()
    {
        try {
            $user = Auth::user();
            $cartItems = $this->getUserCartItems($user);
            $addresses = $this->getUserAddresses($user);

            $summary = $this->calculateOrderSummary($cartItems);

            return $this->successResponse([
                'items' => $this->formatCartItems($cartItems),
                'addresses' => $addresses,
                'payment_summary' => $summary
            ], 'Checkout summary retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get checkout summary: ' . $e->getMessage(), 500);
        }
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($request->address_id);

        DB::beginTransaction();

        try {
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                return $this->errorResponse('Your cart is empty', 400);
            }

            $summary = $this->calculateOrderSummary($cartItems);
            $order = $this->createOrder($user, $address, $summary, $request->payment_method);
            $this->createOrderItems($order, $cartItems);
            $this->updateProductQuantities($cartItems);
            $user->cartItems()->delete();

            DB::commit();

            return $this->successResponse([
                'order_id' => $order->id,
                'total' => $summary['total'],
                'delivery_address' => $this->formatFullAddress($address)
            ], 'Order placed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to place order: ' . $e->getMessage(), 500);
        }
    }

    private function getUserCartItems($user)
    {
        return $user->cartItems()->with(['product' => function($query) {
            $query->with('media');
        }])->get();
    }

    private function getUserAddresses($user)
    {
        return $user->addresses()->get()->map(function ($address) {
            return [
                'id' => $address->id,
                'label' => $address->label,
                'contact_number' => $address->contact_number,
                'full_address' => $this->formatFullAddress($address),
                'is_default' => $address->is_default
            ];
        });
    }

    private function formatCartItems($cartItems)
    {
        return $cartItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'description' => $item->product->description,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->product->featured_image?->url,
                'item_total' => $item->price * $item->quantity
            ];
        });
    }

    private function calculateOrderSummary($cartItems)
    {
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $itemDiscount = $this->calculateItemDiscount($cartItems);
        $shipping = 0;
        $total = $subtotal - $itemDiscount + $shipping;

        return [
            'subtotal' => $subtotal,
            'item_discount' => -$itemDiscount,
            'shipping' => $shipping,
            'total' => $total
        ];
    }

    private function createOrder($user, $address, $summary, $paymentMethod)
    {
        return $user->orders()->create([
            'address_id' => $address->id,
            'subtotal' => $summary['subtotal'],
            'item_discount' => $summary['item_discount'],
            'shipping' => $summary['shipping'],
            'total' => $summary['total'],
            'payment_method' => $paymentMethod,
            'status' => $paymentMethod === 'pay_now' ? 'paid' : 'pending'
        ]);
    }

    private function createOrderItems($order, $cartItems)
    {
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'discount' => 0
            ]);
        }
    }

    private function updateProductQuantities($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $cartItem->product->decrement('quantity', $cartItem->quantity);
        }
    }

    private function calculateItemDiscount($cartItems)
    {
        return 0;
    }

    private function formatFullAddress($address)
    {
        $parts = [
            $address->label,
            $address->contact_number,
            $address->address_line_1,
            $address->address_line_2,
            $address->city,
            $address->state,
            $address->postal_code,
            $address->country
        ];

        return implode(', ', array_filter($parts));
    }
}
