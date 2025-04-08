<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function summary()
    {
        $user = Auth::user();

        $cartItems = $user->cartItems()->with(['product' => function($query) {
            $query->with('media');
        }])->get();

        $items = $cartItems->map(function ($item) {
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

        $subtotal = $items->sum('item_total');
        $itemDiscount = $this->calculateItemDiscount($cartItems);
        $couponDiscount = 0;
        $shipping = 0;

        $total = $subtotal - $itemDiscount - $couponDiscount + $shipping;

        $addresses = $user->addresses()->get()->map(function ($address) {
            return [
                'id' => $address->id,
                'label' => $address->label,
                'contact_number' => $address->contact_number,
                'full_address' => $this->formatFullAddress($address),
                'is_default' => $address->is_default
            ];
        });

        return response()->json([
            'items' => $items,
            'addresses' => $addresses,
            'payment_summary' => [
                'subtotal' => $subtotal,
                'item_discount' => -$itemDiscount,
                'coupon_discount' => -$couponDiscount,
                'shipping' => $shipping,
                'total' => $total
            ]
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $couponDiscount = 15.80;

        return response()->json([
            'message' => 'Coupon applied successfully',
            'coupon_discount' => $couponDiscount
        ]);
    }


    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:pay_now,cod'
        ]);

        $user = Auth::user();

        $address = $user->addresses()->findOrFail($validated['address_id']);

        DB::beginTransaction();

        try {
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Your cart is empty'], 400);
            }

            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $itemDiscount = $this->calculateItemDiscount($cartItems);
            $couponDiscount = 15.80;
            $shipping = 0;
            $total = $subtotal - $itemDiscount - $couponDiscount + $shipping;

            $order = $user->orders()->create([
                'address_id' => $address->id,
                'subtotal' => $subtotal,
                'item_discount' => $itemDiscount,
                'coupon_discount' => $couponDiscount,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['payment_method'] === 'pay_now' ? 'paid' : 'pending'
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'discount' => 0
                ]);

                $cartItem->product->decrement('quantity', $cartItem->quantity);
            }

            $user->cartItems()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'total' => $total,
                'delivery_address' => $this->formatFullAddress($address)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateItemDiscount($cartItems)
    {
        return 28.80;
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
