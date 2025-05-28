<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Branch;
use App\Models\Address;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Filters\GoldPieceFilter;
use App\Traits\ApiResponseTrait;
use App\Events\NewGoldPieceEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\Api\GoldPieceResource;
use App\Http\Resources\Api\OrderRentalResource;
use App\Notifications\NewGoldPieceNotification;
use App\Http\Requests\Api\V1\StoreGoldPieceRequest;
use App\Http\Requests\Api\V1\UpdateGoldPieceRequest;
use App\Notifications\Vendor\NewGoldPieceAvailableNotification;

class GoldPieceController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = GoldPiece::query();
        $filter = new GoldPieceFilter($request);
        $filteredQuery = $filter->apply($query)->with('user');

        $perPage = $request->per_page ?? 15;
        $goldPieces = $filteredQuery->paginate($perPage);

        return $this->successResponse(GoldPieceResource::collection($goldPieces)->response()->getData(true), __("mobile.fetch_gold_pieces_success"));
    }

    public function myGoldPieces(Request $request)
    {
        $query = GoldPiece::query()->where('user_id', Auth::id());

        $filter = new GoldPieceFilter($request);
        $filteredQuery = $filter->apply($query)->with('user');

        $perPage = $request->per_page ?? 15;
        $goldPieces = $filteredQuery->paginate($perPage);

        return $this->successResponse(GoldPieceResource::collection($goldPieces)->response()->getData(true), __("mobile.fetch_gold_pieces_success"));
    }
    public function store(StoreGoldPieceRequest $request)
    {
        try {
            // Get user's default address or first address
            $user = User::with('addresses')->find(Auth::id());
            if (!$user) {
                return $this->errorResponse(__('mobile.User not found'), [], 404);
            }

            $address = $user->addresses()
                ->where('is_default', true)
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orderBy('created_at', 'asc');
                })
                ->first();

            if (!$address) {
                return $this->errorResponse(
                    __('mobile.User must have at least one address to create a gold piece'),
                    [],
                    422
                );
            }

            // Get active branches in the same city as the user's address
            $branches = Branch::query()
                ->where('city_id', $address->city_id)
                ->where('is_active', true)
                ->whereHas('vendor', function ($query) {
                    $query->where('is_active', true);
                })
                ->get();

            if ($branches->isEmpty()) {
                return $this->errorResponse(
                    __('mobile.No active branches found in your city'),
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
                        'status' => OrderRental::STATUS_PENDING_APPROVAL,
                        'total_price' => $goldPiece->rental_price_per_day,
                        'type' => OrderRental::RENT_TYPE,
                    ]);
                } else {
                    OrderSale::create([
                        'user_id' => $user->id,
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'status' => OrderSale::STATUS_PENDING_APPROVAL,
                        'total_price' => $goldPiece->sale_price,
                    ]);
                }

                // Send database notification
                $branch->notify(new NewGoldPieceNotification($goldPiece));

                // Broadcast event
                broadcast(new NewGoldPieceEvent($goldPiece, $branch->id))->toOthers();
            }

            DB::commit();

            // Get unique vendors from the branches we created orders for
            $vendors = collect($branches)
                ->pluck('vendor') // Get all vendors from branches
                ->unique('id')    // Remove duplicates
                ->filter();       // Remove null values

            // Notify each vendor
            foreach ($vendors as $vendor) {
                $vendor->notify(new NewGoldPieceAvailableNotification($goldPiece));
            }
            // Load the media relationship before returning the resource
            $goldPiece->load('user');
            return $this->successResponse(
                new GoldPieceResource($goldPiece),
                __('mobile.Gold piece created successfully')
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create gold piece: ' . $e->getMessage());
            return $this->errorResponse(
                __('mobile.Failed to create gold piece'),
                ['error' => $e->getMessage()]
            );
        }
    }

    public function show(GoldPiece $goldPiece)
    {
        return $this->successResponse(new GoldPieceResource($goldPiece->load('user')), __('mobile.Gold piece fetched successfully'));
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
                return $this->errorResponse(__('mobile.Unauthorized. You can only delete your own gold pieces.'), [], 403);
            }

            // Check if the gold piece has any active rentals or sales
            if (
                $goldPiece->orderRentals()->whereIn('status', ['pending', 'active'])->exists() ||
                $goldPiece->orderSales()->whereIn('status', ['pending', 'processing'])->exists()
            ) {
                return $this->errorResponse(__('mobile.Cannot delete gold piece with active orders.'), [], 400);
            }

            DB::beginTransaction();

            // Delete associated media first
            $goldPiece->clearMediaCollection('images');

            // Delete the gold piece
            $goldPiece->delete();

            DB::commit();

            return $this->successResponse(null, __('mobile.Gold piece deleted successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete gold piece: ' . $e->getMessage());
            return $this->errorResponse(__('mobile.Failed to delete gold piece'), ['error' => $e->getMessage()]);
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
                return $this->errorResponse(__('mobile.Unauthorized. You can only update your own gold pieces.'), [], 403);
            }

            // Check if the gold piece has active orders before allowing type change
            if ($request->has('type') && $request->type !== $goldPiece->type) {
                if (
                    $goldPiece->orderRentals()->whereIn('status', ['pending', 'active'])->exists() ||
                    $goldPiece->orderSales()->whereIn('status', ['pending', 'processing'])->exists()
                ) {
                    return $this->errorResponse(__('mobile.Cannot change type while there are active orders.'), [], 400);
                }
            }

            DB::beginTransaction();

            // Update basic information
            $updateData = $request->only([
                'name',
                'weight',
                'carat',
                'type',
                'description',
                'rental_price_per_day',
                'sale_price'
            ]);

            // Calculate deposit amount if type is for_rent and weight or carat changed
            if (
                $request->type === 'for_rent' ||
                ($goldPiece->type === 'for_rent' && !$request->has('type'))
            ) {
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
                __('mobile.Gold piece updated successfully')
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update gold piece: ' . $e->getMessage());
            return $this->errorResponse(__('mobile.Failed to update gold piece'), ['error' => $e->getMessage()]);
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

    public function goldPiecesWillFinishRentalSoon()
    {
        $orderRentals = OrderRental::where('end_date', '<', now())
            ->with('goldPiece')
            ->limit(3)
            ->get();

        return $this->successResponse(
            OrderRentalResource::collection($orderRentals), 
            __('mobile.Gold pieces will finish rental soon')
        );
    }
}