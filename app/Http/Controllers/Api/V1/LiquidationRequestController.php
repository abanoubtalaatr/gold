<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Models\LiquidationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Core\ApiHelperTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\LiquidationRequestResource;
use App\Http\Requests\Api\V1\LiquidationRequest\StoreLiquidationRequest;

class LiquidationRequestController extends Controller
{
    use ApiHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liquidationRequests = LiquidationRequest::where('user_id', Auth::id())
            ->latest()
            ->paginate();

        return $this->successResponse(
            'Liquidation requests fetched successfully',
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

        return $this->successResponse(
            'Liquidation request created successfully',
            new LiquidationRequestResource($liquidationRequest)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(LiquidationRequest $liquidationRequest): JsonResponse
    {
        $this->authorize('view', $liquidationRequest);

        return $this->successResponse(
            'Liquidation request fetched successfully',
            new LiquidationRequestResource($liquidationRequest)
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
            'Liquidation request deleted successfully',
            new LiquidationRequestResource($liquidationRequest)
        );
    }
} 