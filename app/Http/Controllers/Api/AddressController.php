<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAddress;
use App\Traits\ApiResponse;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $addresses = Auth::user()->addresses()->get();

            return $this->successResponse(
                $addresses->map(function ($address) {
                    return $this->formatAddressResponse($address, true);
                }),
                'Addresses retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve addresses: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreAddressRequest $request)
    {
        try {
            $user = Auth::user();

            if ($request->is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address = $user->addresses()->create($request->validated());

            return $this->successResponse(
                $this->formatAddressResponse($address),
                'Address added successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add address: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateAddressRequest $request, UserAddress $address)
    {
        try {
            if ($address->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized action', 403);
            }

            if ($request->is_default) {
                Auth::user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->validated());

            return $this->successResponse(
                $this->formatAddressResponse($address),
                'Address updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update address: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(UserAddress $address)
    {
        try {
            if ($address->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized action', 403);
            }

            $address->delete();

            return $this->successResponse(
                null,
                'Address deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete address: ' . $e->getMessage(), 500);
        }
    }

    public function setDefault(UserAddress $address)
    {
        try {
            if ($address->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized action', 403);
            }

            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);

            return $this->successResponse(
                $this->formatAddressResponse($address),
                'Default address set successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to set default address: ' . $e->getMessage(), 500);
        }
    }

    private function formatAddressResponse(UserAddress $address, $fullDetails = false)
    {
        $response = [
            'id' => $address->id,
            'label' => $address->label,
            'contact_number' => $address->contact_number,
            'full_address' => $this->formatFullAddress($address),
            'is_default' => $address->is_default
        ];

        if ($fullDetails) {
            $response = array_merge($response, [
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
            ]);
        }

        return $response;
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
