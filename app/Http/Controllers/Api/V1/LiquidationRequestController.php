<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LiquidationRequest\StoreLiquidationRequest;
use App\Http\Resources\Api\V1\LiquidationRequestResource;
use App\Models\LiquidationRequest;
use App\Models\User;
use App\Notifications\Admin\NewLiquidationRequest;
use App\Traits\ApiResponseTrait;
use Google\Cloud\Core\ApiHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LiquidationRequestController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liquidationRequests = LiquidationRequest::where('user_id', Auth::id())
            ->latest()
            ->paginate();

        return $this->successResponse(
            __('mobile.Liquidation requests fetched successfully'),
            LiquidationRequestResource::collection($liquidationRequests)->response()->getData(true)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLiquidationRequest $request): JsonResponse
    {
        $liquidationRequest = LiquidationRequest::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            ...$request->validated(),
        ]);

        // Notify all admins
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin')
                ->orWhere('name', 'superadmin')
                ->whereNull('vendor_id');
        })->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewLiquidationRequest($liquidationRequest));
        }

        return $this->successResponse(
            new LiquidationRequestResource($liquidationRequest),
            __('mobile.Liquidation request created successfully')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(LiquidationRequest $liquidationRequest): JsonResponse
    {
        $this->authorize('view', $liquidationRequest);

        return $this->successResponse(
            new LiquidationRequestResource($liquidationRequest),
            __('mobile.Liquidation request fetched successfully')
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiquidationRequest $liquidationRequest): JsonResponse
    {
        $this->authorize('delete', $liquidationRequest);

        $liquidationRequest->delete();

        return $this->successResponse(
            new LiquidationRequestResource($liquidationRequest),
            __('mobile.Liquidation request deleted successfully')
        );
    }
}
