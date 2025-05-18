<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GoldPiece;
use App\Models\User;
use App\Models\Branch;
use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\GoldPieceResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\Api\V1\StoreGoldPieceRequest;
use App\Http\Requests\Api\V1\UpdateGoldPieceRequest;
use App\Notifications\NewGoldPieceNotification;
use App\Events\NewGoldPieceEvent;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Models\Address;

class GoldPieceController extends Controller
{
    use ApiResponseTrait;
    
    public function index(Request $request)
    {
        $query = GoldPiece::query()
            // Carat range filter
            ->when($request->filled(['from_carat', 'to_carat']), function($q) use ($request) {
                $q->whereBetween('carat', [$request->from_carat, $request->to_carat]);
            })
            ->when($request->carat, fn($q) => $q->where('carat', $request->carat))

            // Weight range filter
            ->when($request->filled(['from_weight', 'to_weight']), function($q) use ($request) {
                $q->whereBetween('weight', [$request->from_weight, $request->to_weight]);
            })
            ->when($request->weight, fn($q) => $q->where('weight', $request->weight))

            // Price range filter for rental items
            ->when($request->filled(['from_rental_price', 'to_rental_price']), function($q) use ($request) {
                $q->where('type', 'for_rent')
                  ->whereBetween('rental_price_per_day', [$request->from_rental_price, $request->to_rental_price]);
            })

            // Price range filter for sale items
            ->when($request->filled(['from_sale_price', 'to_sale_price']), function($q) use ($request) {
                $q->where('type', 'for_sale')
                  ->whereBetween('sale_price', [$request->from_sale_price, $request->to_sale_price]);
            })

            // Type filter
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            
            // Status filter
            ->when($request->status, fn($q) => $q->where('status', $request->status))

            // Search by name
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))

            // Sort by price
            ->when($request->price_sort, function($q) use ($request) {
                $column = $request->type === 'for_rent' ? 'rental_price_per_day' : 'sale_price';
                $q->orderBy($column, $request->price_sort);
            })

            ->with('user');

        $perPage = $request->per_page ?? 15; // Default 15 items per page
        $goldPieces = $query->paginate($perPage);

        return $this->successResponse(
            GoldPieceResource::collection($goldPieces)->response()->getData(true),
            'Gold pieces fetched successfully'
        );
    }

    public function store(StoreGoldPieceRequest $request)
    {
        try {
            // Get user's default address or first address
            $user = User::with('addresses')->find(Auth::id());
            if (!$user) {
                return $this->errorResponse('User not found', [], 404);
            }

            $address = $user->addresses()
                ->where('is_default', true)
                ->orWhere(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orderBy('created_at', 'asc');
                })
                ->first();

            if (!$address) {
                return $this->errorResponse(
                    'User must have at least one address to create a gold piece', 
                    [], 
                    422
                );
            }

            // Get active branches in the same city as the user's address
            $branches = Branch::query()
                ->where('city_id', $address->city_id)
                ->where('is_active', true)
                ->whereHas('vendor', function($query) {
                    $query->where('is_active', true);
                })
                ->get();

            if ($branches->isEmpty()) {
                return $this->errorResponse(
                    'No active branches found in your city', 
                    [], 
                    422
                );
            }

            DB::beginTransaction();

            $goldPiece = GoldPiece::create([
                'name' => $request->name,
                'weight' => $request->weight,
                'carat' => $request->carat,
                'user_id' => $user->id,
                'type' => $request->type,
                'status' => 'pending',
                'description' => $request->description,
                'rental_price_per_day' => $request->type === 'for_rent' ? $request->rental_price_per_day : null,
                'sale_price' => $request->type === 'for_sale' ? $request->sale_price : null,
                'deposit_amount' => $request->type === 'for_rent' ? $request->deposit_amount : null,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        $goldPiece->addMedia($image)
                            ->toMediaCollection('images', 'public');
                    } catch (\Exception $e) {
                        Log::error('Failed to upload image: ' . $e->getMessage());
                        continue;
                    }
                }
            }

            // Generate QR code
            // $qrCode = QrCode::format('png')
            //     ->size(200)
            //     ->generate(route('api.v1.gold_pieces.show', $goldPiece->id));

            // $goldPiece->update([
            //     'qr_code' => $qrCode
            // ]);

            // Create orders for each branch
            foreach ($branches as $branch) {
                if ($request->type === 'for_rent') {
                    OrderRental::create([
                        'user_id' => $user->id,
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'status' => 'pending',
                        'total_price' => $goldPiece->rental_price_per_day,
                    ]);
                } else {
                    OrderSale::create([
                        'user_id' => $user->id,
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'status' => 'pending',
                        'total_price' => $goldPiece->sale_price,
                    ]);
                }

                // Send database notification
                $branch->notify(new NewGoldPieceNotification($goldPiece));

                // Broadcast event
                broadcast(new NewGoldPieceEvent($goldPiece, $branch->id))->toOthers();
            }

            DB::commit();

            // Load the media relationship before returning the resource
            $goldPiece->load('user');
            return $this->successResponse(
                new GoldPieceResource($goldPiece), 
                'Gold piece created successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create gold piece: ' . $e->getMessage());
            return $this->errorResponse(
                'Failed to create gold piece', 
                ['error' => $e->getMessage()]
            );
        }
    }

    public function show(GoldPiece $goldPiece)
    {
        return $this->successResponse(new GoldPieceResource($goldPiece->load('user')), 'Gold piece fetched successfully');
    }

    /**
     * Delete a gold piece if the authenticated user is the owner
     *
     * @param GoldPiece $goldPiece
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(GoldPiece $goldPiece)
    {
        try {
            if ($goldPiece->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized. You can only delete your own gold pieces.', [], 403);
            }

            // Check if the gold piece has any active rentals or sales
            if ($goldPiece->orderRentals()->whereIn('status', ['pending', 'active'])->exists() ||
                $goldPiece->orderSales()->whereIn('status', ['pending', 'processing'])->exists()) {
                return $this->errorResponse('Cannot delete gold piece with active orders.', [], 400);
            }

            DB::beginTransaction();

            // Delete associated media first
            $goldPiece->clearMediaCollection('images');
            
            // Delete the gold piece
            $goldPiece->delete();

            DB::commit();

            return $this->successResponse(null, 'Gold piece deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete gold piece: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete gold piece', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update a gold piece
     *
     * @param UpdateGoldPieceRequest $request
     * @param GoldPiece $goldPiece
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGoldPieceRequest $request, GoldPiece $goldPiece)
    {
        try {
            if ($goldPiece->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized. You can only update your own gold pieces.', [], 403);
            }

            // Check if the gold piece has active orders before allowing type change
            if ($request->has('type') && $request->type !== $goldPiece->type) {
                if ($goldPiece->orderRentals()->whereIn('status', ['pending', 'active'])->exists() ||
                    $goldPiece->orderSales()->whereIn('status', ['pending', 'processing'])->exists()) {
                    return $this->errorResponse('Cannot change type while there are active orders.', [], 400);
                }
            }

            DB::beginTransaction();

            // Update basic information
            $updateData = $request->only([
                'name', 'weight', 'carat', 'type', 'description',
                'rental_price_per_day', 'sale_price'
            ]);

            // Calculate deposit amount if type is for_rent and weight or carat changed
            if ($request->type === 'for_rent' || 
                ($goldPiece->type === 'for_rent' && !$request->has('type'))) {
                if ($request->has('weight') || $request->has('carat')) {
                    $weight = $request->weight ?? $goldPiece->weight;
                    $carat = $request->carat ?? $goldPiece->carat;
                    $updateData['deposit_amount'] = $this->calculateDeposit($weight, $carat);
                }
            }

            // Remove specified images
            if ($request->has('remove_image_ids')) {
                foreach ($request->remove_image_ids as $mediaId) {
                    $media = $goldPiece->media()->find($mediaId);
                    if ($media && $media->collection_name === 'images') {
                        $media->delete();
                    }
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        $goldPiece->addMedia($image)
                            ->toMediaCollection('images', 'public');
                    } catch (\Exception $e) {
                        Log::error('Failed to upload image: ' . $e->getMessage());
                        continue;
                    }
                }
            }

            $goldPiece->update($updateData);

            DB::commit();

            return $this->successResponse(
                new GoldPieceResource($goldPiece->fresh()),
                'Gold piece updated successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update gold piece: ' . $e->getMessage());
            return $this->errorResponse('Failed to update gold piece', ['error' => $e->getMessage()]);
        }
    }

    private function calculateRentalPrice($weight, $carat)
    {
        $marketPricePerGram = 250;
        return $weight * $marketPricePerGram * ($carat / 24) * 0.05;
    }

    private function calculateSalePrice($weight, $carat)
    {
        $marketPricePerGram = 250;
        return $weight * $marketPricePerGram * ($carat / 24);
    }

    private function calculateDeposit($weight, $carat)
    {
        return $this->calculateRentalPrice($weight, $carat) * 10;
    }
}