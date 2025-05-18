<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LiquidationRequest\StoreLiquidationRequest;
use App\Models\LiquidationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LiquidationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $liquidationRequests = LiquidationRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate();

        return JsonResource::collection($liquidationRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLiquidationRequest $request): JsonResponse
    {
        $liquidationRequest = LiquidationRequest::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            ...$request->validated(),
        ]);

        return response()->json([
            'message' => __('messages.liquidation_request.created'),
            'data' => new JsonResource($liquidationRequest),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LiquidationRequest $liquidationRequest): JsonResponse
    {
        $this->authorize('view', $liquidationRequest);

        return response()->json([
            'data' => new JsonResource($liquidationRequest),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiquidationRequest $liquidationRequest): JsonResponse
    {
        $this->authorize('delete', $liquidationRequest);

        $liquidationRequest->delete();

        return response()->json([
            'message' => __('messages.liquidation_request.deleted'),
        ]);
    }
} 