<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GoldPiece;
use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GoldPieceResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get the authenticated user's favorite gold pieces.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 15;
            $favorites = Auth::user()
                ->favoritedGoldPieces()
                ->with('user')
                ->paginate($perPage);

            return $this->successResponse(
                GoldPieceResource::collection($favorites)->response()->getData(true),
                'Favorites fetched successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch favorites: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch favorites', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Toggle favorite status for a gold piece.
     *
     * @param GoldPiece $goldPiece
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(GoldPiece $goldPiece)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $favorite = $user->favorites()
                ->where('gold_piece_id', $goldPiece->id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                $message = 'Gold piece removed from favorites';
                $isFavorited = false;
            } else {
                $user->favorites()->create([
                    'gold_piece_id' => $goldPiece->id
                ]);
                $message = 'Gold piece added to favorites';
                $isFavorited = true;
            }

            DB::commit();

            return $this->successResponse(
                ['is_favorited' => $isFavorited],
                $message
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle favorite: ' . $e->getMessage());
            return $this->errorResponse('Failed to update favorite status', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if a gold piece is favorited by the authenticated user.
     *
     * @param GoldPiece $goldPiece
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(GoldPiece $goldPiece)
    {
        try {
            $isFavorited = Auth::user()
                ->favorites()
                ->where('gold_piece_id', $goldPiece->id)
                ->exists();

            return $this->successResponse(
                ['is_favorited' => $isFavorited],
                'Favorite status checked successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to check favorite status: ' . $e->getMessage());
            return $this->errorResponse('Failed to check favorite status', ['error' => $e->getMessage()]);
        }
    }
} 