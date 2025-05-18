<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Address;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\AddressResource;
use App\Http\Requests\Api\V1\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    // List all addresses
    public function index(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $query = $user->addresses()
                ->with(['city']);

            // Add any filters here if needed
            $perPage = $request->per_page ?? 15; // Default 15 items per page
            $addresses = $query->paginate($perPage);
            
            return $this->successResponse(
                AddressResource::collection($addresses)->response()->getData(true),
                'Addresses retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve addresses',
                ['error' => $e->getMessage()]
            );
        }
    }

    // Create new address
    public function store(AddressRequest $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // If setting as default, first unset any existing default
            if ($request->is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address = $user->addresses()->create($request->validated());

            return $this->successResponse(
                new AddressResource($address->load(['country', 'state', 'city'])),
                'Address added successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to add address',
                ['error' => $e->getMessage()]
            );
        }
    }

    // Show single address
    public function show($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $address = $user->addresses()
                ->with(['country', 'state', 'city'])
                ->findOrFail($id);
            
            return $this->successResponse(
                new AddressResource($address),
                'Address retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Address not found',
                ['error' => $e->getMessage()],
                404
            );
        }
    }

    // Update address
    public function update(AddressRequest $request, $id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // If setting as default, first unset any existing default
            if ($request->is_default) {
                $user->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->validated());

            return $this->successResponse(
                new AddressResource($address->load(['country', 'state', 'city'])),
                'Address updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update address',
                ['error' => $e->getMessage()]
            );
        }
    }

    // Delete address
    public function destroy($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Prevent deletion if it's the last address
            if ($user->addresses()->count() <= 1) {
                return $this->errorResponse(
                    'Cannot delete the last address',
                    null,
                    422
                );
            }

            $address->delete();

            return $this->successResponse(
                null,
                'Address deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete address',
                ['error' => $e->getMessage()]
            );
        }
    }

    // Set default address
    public function setDefault($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Unset all other defaults
            $user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            // Set this one as default
            $address->update(['is_default' => true]);

            return $this->successResponse(
                new AddressResource($address->load(['country', 'state', 'city'])),
                'Default address set successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to set default address',
                ['error' => $e->getMessage()]
            );
        }
    }
}