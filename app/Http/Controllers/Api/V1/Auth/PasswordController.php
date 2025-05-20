<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MobileConfirm;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdatePasswordRequest;
/**
 * Class PasswordController.
 */
class PasswordController extends Controller
{
    use ApiResponseTrait;

    /**
     * @group User Profile
     *
     * Update Password Request.
     *
     * Requires Authorization by bearer token
     * 
     * @bodyParam current_password string required the old password for the user
     * @bodyParam password string required the new password for the user
     * @bodyParam password_confirmation string required confrim the new password for the user
     *
     * @param UpdatePasswordRequest $request
     *
     * @throws \App\Exceptions\GeneralException
     * @return mixed
     */
    public function update(UpdatePasswordRequest $request)
    {
        try {

            $this->updatePassword(auth('api')->user(),$request->validated());

            return $this->successResponse(
                [],
                __('mobile.Password updated successfully')
            );

        } catch (Exception $e) {

            return $this->errorResponse(
                $e->getMessage(),
                [],
                $e->getCode()
            );
        }
    }

    /**
     * @group User Login
     *
     * Reset the given user's password.
     *
     * @bodyParam code string required the password reset code for the user
     * @bodyParam password string required the new password for the user
     * @bodyParam password_confirmation string required confrim the new password for the user
     * @bodyParam email email required the email for the user
     *
     * @param  ResetPasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(ResetPasswordRequest $request)
    {

        if($request->has('email')){
            $user = User::where('email',$request->email)->first();
        }else{

            $mobile = mobileHandler($request->mobile);

            $user = User::where('mobile',$mobile)->where('dialling_code',$request->dialling_code)->first();
        
            if($user){
                $record = MobileConfirm::where('code', $request->code)
                ->where('mobile', $mobile)
                ->where('dialling_code', $request->dialling_code)->first();

                if ($record) {
                    $this->resetPassword($user, $request->password);
                return $this->sendResetResponse('passwords.reset');
                }
            }

            return $this->sendResetFailedResponse($request, "passwords.token");
        }

        if ($user && resolve('auth.password.broker')->tokenExists($user, $request->code)) {

            $request->merge([
                'email' => $user->email,
                'token' => $request->code,
                'type' => $request->type,
            ]);


            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $response = $this->broker()->reset(
                $this->credentials($request),
                function ($user, $password) {
                    $this->resetPassword($user,$password);
                }
            );
    
            return $response === Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
        }
    
        return $this->sendResetFailedResponse($request, "passwords.token");
    }


    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     */
    protected function resetPassword($user, $password)
    {
        $user->password =  Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        return $this->successResponse(
            [],
            e(trans($response))
        );

    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {

        $message = trans($response);
        
        if ($response === Password::INVALID_TOKEN) {
            $message = trans('passwords.invalid_token');
        } else {
            if ($response === Password::INVALID_USER) {
                $message = trans('passwords.invalid_user');
            }
        }
        return $this->errorResponse(
        $message,
            [],
            400
        );
    }


    /**
     * @param  User  $user
     * @param $data
     * @param  bool  $expired
     *
     * @return User
     * @throws \Throwable
     */
    public function updatePassword(User $user, $data, $expired = false): User
    {
        if (isset($data['current_password'])) {
            throw_if(! Hash::check($data['current_password'], $user->password),
                new Exception(__('Your old password is wrong.'))
            );
        }

        $user->password = Hash::make($data['password']);

        return tap($user)->update();
    }
}