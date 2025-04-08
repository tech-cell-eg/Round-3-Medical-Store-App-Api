<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->get();
        
        return response()->json([
            'addresses' => $addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->label,
                    'contact_number' => $address->contact_number,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'is_default' => $address->is_default,
                    'full_address' => $this->formatFullAddress($address)
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'sometimes|string|max:255',
            'is_default' => 'boolean'
        ]);

        $user = Auth::user();

        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($validated);

        return response()->json([
            'message' => 'Address added successfully',
            'address' => $this->formatAddressResponse($address)
        ], 201);
    }

    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'label' => 'sometimes|string|max:255',
            'contact_number' => 'sometimes|string|max:20',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'is_default' => 'sometimes|boolean'
        ]);

        if ($request->is_default) {
            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $this->formatAddressResponse($address)
        ]);
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        Auth::user()->addresses()
            ->where('id', '!=', $address->id)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Default address set successfully',
            'address' => $this->formatAddressResponse($address)
        ]);
    }

    private function formatAddressResponse(UserAddress $address)
    {
        return [
            'id' => $address->id,
            'label' => $address->label,
            'contact_number' => $address->contact_number,
            'full_address' => $this->formatFullAddress($address),
            'is_default' => $address->is_default
        ];
    }

    private function formatFullAddress(UserAddress $address)
    {
        $parts = [
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
