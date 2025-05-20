<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Rating;
use App\Models\GoldPiece;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Api\RatingResource;
use App\Http\Requests\Api\V1\RatingRequest;

class RatingController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get ratings for a specific gold piece.
     */
    public function index(GoldPiece $goldPiece)
    {
        try {
            $ratings = $goldPiece->ratings()
                ->with('user')
                ->latest()
                ->paginate(10);

            return $this->successResponse(
                RatingResource::collection($ratings)->response()->getData(true),
                __('mobile.fetch_ratings_success')
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch ratings: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch ratings', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a new rating.
     */
    public function store(RatingRequest $request, GoldPiece $goldPiece)
    {
        try {
            // Check if user has already rated this gold piece
            $existingRating = Rating::where('user_id', Auth::id())
                ->where('gold_piece_id', $goldPiece->id)
                ->first();

            if ($existingRating) {
                return $this->errorResponse(
                    __('mobile.already_rated'),
                    ['error' => 'You have already rated this gold piece'],
                    409
                );
            }

            $rating = Rating::create([
                'user_id' => Auth::id(),
                'gold_piece_id' => $goldPiece->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return $this->successResponse(
                new RatingResource($rating->load('user', 'goldPiece')),
                __('mobile.rating_created_success')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create rating: ' . $e->getMessage());
            return $this->errorResponse('Failed to create rating', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing rating.
     */
    public function update(RatingRequest $request, Rating $rating)
    {
        try {
            if ($rating->user_id !== Auth::id()) {
                return $this->errorResponse(
                    __('mobile.unauthorized'),
                    ['error' => 'You can only update your own ratings'],
                    403
                );
            }

            $rating->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return $this->successResponse(
                new RatingResource($rating->load('user', 'goldPiece')),
                __('mobile.rating_updated_success')
            );
        } catch (\Exception $e) {
            Log::error('Failed to update rating: ' . $e->getMessage());
            return $this->errorResponse('Failed to update rating', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a rating.
     */
    public function destroy(Rating $rating)
    {
        try {
            if ($rating->user_id !== Auth::id()) {
                return $this->errorResponse(
                    __('mobile.unauthorized'),
                    ['error' => 'You can only delete your own ratings'],
                    403
                );
            }

            $rating->delete();

            return $this->successResponse(
                null,
                __('mobile.rating_deleted_success')
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete rating: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete rating', ['error' => $e->getMessage()]);
        }
    }
} 