<?php 

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Requests\Api\Profile\ChangePasswordRequest;
use App\Http\Requests\Api\Profile\DeleteAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            return $this->successResponse(new UserResource($user),'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving profile',['error' => $e->getMessage()]);
        }
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            $data = $request->validated();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if it exists and is not the default
                if ($user->avatar && !str_contains($user->avatar, 'default_avatar')) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                // Store new avatar
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($data);

            return $this->successResponse(new UserResource($user),'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error updating profile',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', null, 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return $this->successResponse(
                null,
                'Password changed successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error changing password',
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Delete the user's account.
     *
     * @param DeleteAccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = Auth::user();

            // Begin transaction
            DB::beginTransaction();

            // Delete related data
            $user->addresses()->delete();
            $user->deviceTokens()->delete();
            
            // Revoke all tokens
            $user->tokens()->delete();

            // Delete the user
            $user->delete();

            DB::commit();

            return $this->successResponse(null, 'Account deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete account', ['error' => $e->getMessage()]);
        }
    }
}