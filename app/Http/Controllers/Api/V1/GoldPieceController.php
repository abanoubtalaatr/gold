<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Filters\GoldPieceFilter;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\Vendor\VendorNotification;
use App\Services\VendorNotificationService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\Api\GoldPieceResource;
use App\Http\Resources\Api\OrderRentalResource;
use App\Http\Requests\Api\V1\StoreGoldPieceRequest;
use App\Http\Requests\Api\V1\UpdateGoldPieceRequest;

class GoldPieceController extends Controller
{
    use ApiResponseTrait;

    protected $vendorNotificationService;

    public function __construct(VendorNotificationService $vendorNotificationService)
    {
        $this->vendorNotificationService = $vendorNotificationService;
    }

    public function index(Request $request)
    {
        $query = GoldPiece::query();
        $filter = new GoldPieceFilter($request);

        $filteredQuery = $filter->apply($query)->with('user')->whereHas('orderRentals', function ($query) {
            $query->where('status', 'vendor_already_take_the_piece');
        });


        $perPage = $request->per_page ?? 15;
        $goldPieces = $filteredQuery->paginate($perPage);

        return $this->successResponse(GoldPieceResource::collection($goldPieces)->response()->getData(true), __("mobile.fetch_gold_pieces_success"));
    }

    public function myGoldPieces(Request $request)
    {
        $query = GoldPiece::query()->where('user_id', Auth::id());

        $filter = new GoldPieceFilter($request);

        $filteredQuery = $filter->apply($query)->with('user');

        if ($request->has('type') && $request->type === 'for_rent') {
            $filteredQuery->whereHas('orderRentals');
        } else {
            $filteredQuery->whereHas('orderSales');
        }
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
                    $query->where('vendor_status', 'approved');
                })
                ->with('vendor') // Eager load vendor for notifications
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
                'is_including_lobes' => $request->is_including_lobes,
            ]);

            // Generate QR code for the gold piece and store as media
            $qrCodeUrl = route('gold-piece.show', $goldPiece->id);

            try {
                // Generate QR code as SVG (doesn't require imagick extension)
                $qrCodeImage = QrCode::format('svg')->size(200)->generate($qrCodeUrl);

                // Create a temporary file stream in memory
                $tempStream = fopen('php://temp', 'r+');
                fwrite($tempStream, $qrCodeImage);
                rewind($tempStream);

                // Add QR code as media using the stream
                $qrCodeMedia = $goldPiece->addMediaFromStream($tempStream)
                    ->usingName('QR Code for ' . $goldPiece->name)
                    ->usingFileName('qr_code_' . $goldPiece->id . '.svg')
                    ->toMediaCollection('qr_codes', 'public');

                // Close the stream
                fclose($tempStream);

                // Store the media URL in the qr_code field for backward compatibility
                $goldPiece->update(['qr_code' => $qrCodeMedia->getUrl()]);
            } catch (\Exception $e) {
                Log::error('Failed to generate QR code with media library: ' . $e->getMessage());

                try {
                    // Secondary fallback: store SVG content directly in database
                    $qrCodeImage = QrCode::format('svg')->size(200)->generate($qrCodeUrl);
                    $goldPiece->update(['qr_code' => $qrCodeImage]);
                } catch (\Exception $e2) {
                    Log::error('Failed to generate QR code completely: ' . $e2->getMessage());
                    // Final fallback: store QR code URL directly in database
                    $goldPiece->update(['qr_code' => $qrCodeUrl]);
                }
            }

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

            // Create orders for each branch
            $createdOrders = [];
            $vendorOrders = []; // Group orders by vendor
            foreach ($branches as $branch) {
                if ($request->type === 'for_rent') {
                    $order = OrderRental::create([
                        'user_id' => $user->id,
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'status' => OrderRental::STATUS_PENDING_APPROVAL,
                        'total_price' => $goldPiece->rental_price_per_day,
                        'type' => OrderRental::RENT_TYPE,
                    ]);
                    $createdOrders[] = ['order' => $order, 'type' => 'rental'];

                    // Group by vendor for notifications
                    if (!isset($vendorOrders[$branch->vendor_id])) {
                        $vendorOrders[$branch->vendor_id] = [];
                    }
                    $vendorOrders[$branch->vendor_id][] = ['order' => $order, 'type' => 'rental'];
                } else {
                    $order = OrderSale::create([
                        'user_id' => $user->id,
                        'gold_piece_id' => $goldPiece->id,
                        'branch_id' => $branch->id,
                        'status' => OrderSale::STATUS_PENDING_APPROVAL,
                        'total_price' => $goldPiece->sale_price,
                    ]);
                    $createdOrders[] = ['order' => $order, 'type' => 'sale'];

                    // Group by vendor for notifications
                    if (!isset($vendorOrders[$branch->vendor_id])) {
                        $vendorOrders[$branch->vendor_id] = [];
                    }
                    $vendorOrders[$branch->vendor_id][] = ['order' => $order, 'type' => 'sale'];
                }
            }

            DB::commit();

            // Send vendor notifications - one per vendor instead of per branch
            foreach ($vendorOrders as $vendorId => $orders) {
                try {
                    // Send notification using the first order for this vendor
                    // This ensures one notification per vendor regardless of how many branches they have
                    $firstOrder = $orders[0];
                    $this->vendorNotificationService->notifyVendorOfNewOrder(
                        $firstOrder['order'],
                        $firstOrder['type']
                    );
                    // i want to make the sound working fine, then making prefer language for vendor or user 
                    // and then get it to send the notification instead of the current one
                    
                    $user = User::find($firstOrder['order']->branch->vendor->id);
                    $type = $firstOrder['type'] === 'rental' ?'للايجار' : 'للبيع';
                    $title = $user->prefer_language === 'ar' ? ' طلب جديد '  : __('mobile.New Request') . ' ' . $type;
                    $message = $user->prefer_language === 'ar' ? 'لديك طلب جديد ' . $type : __('mobile.You have a new request') . ' ' . $type;
                    event(new VendorNotification($title, $message, $firstOrder['order']->branch->vendor->id));
                } catch (\Exception $e) {
                }
            }

            // Load the media relationship before returning the resource
            $goldPiece->load('user');
            return $this->successResponse(
                new GoldPieceResource($goldPiece),
                __('mobile.Gold piece created successfully')
            );
        } catch (\Exception $e) {
            DB::rollBack();
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
            // if ($goldPiece->user_id !== Auth::id()) {
            //     return $this->errorResponse(__('mobile.Unauthorized. You can only delete your own gold pieces.'), [], 403);
            // }

            // // Check if the gold piece has any active rentals or sales
            // if (
            //     $goldPiece->orderRentals()->whereIn('status', ['pending', 'active'])->exists() ||
            //     $goldPiece->orderSales()->whereIn('status', ['pending', 'processing'])->exists()
            // ) {
            //     return $this->errorResponse(__('mobile.Cannot delete gold piece with active orders.'), [], 400);
            // }

            DB::beginTransaction();

            // Delete associated media first
            $goldPiece->clearMediaCollection('images');
            $goldPiece->clearMediaCollection('qr_codes');

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
        $orderRentals = OrderRental::whereBetween('end_date', [now(), now()->addDays(3)])
            ->with('goldPiece')
            ->where('status', OrderRental::STATUS_VENDOR_ALREADY_TAKE_THE_PIECE)
            ->limit(3)
            ->get();

        return $this->successResponse(
            OrderRentalResource::collection($orderRentals),
            __('mobile.Gold pieces will finish rental soon')
        );
    }
}
