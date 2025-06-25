<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;
use Carbon\Carbon;
use App\Helpers\OTP;
use App\Models\User;
use App\Services\SMS\Msegat;
use Illuminate\Http\Request;
use App\Models\MobileConfirm;
use App\Events\User\UserLoggedIn;
use App\Events\User\ApiRegistered;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MesgatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Wotz\VerificationCode\VerificationCode;
use App\Http\Controllers\Api\AppBaseController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\MobileConfirmRequest;
use App\Http\Requests\Api\Auth\VerifyAccountRequest;


/**
 * Class RegisterController.
 */
class RegisterController extends AppBaseController
{

    protected $smsService;

    /**
     * RegisterController constructor.
     */
    public function __construct(Msegat $sms)
    {
        $this->smsService = $sms;
    }

    public function register(RegisterRequest $request)
    {

        // try {

            $data = $request->all();

            $mobile = mobileHandler($request->mobile);

            $data = array_merge($data, ['mobile' => $mobile]);

            if ($request->hasFile('avatar')) {
                $data = storeFile('avatar', '/users', $request->file('avatar'), $data);
            }

            $user = $this->create($data);

            // $user->unsetRelations(['media', 'roles']);

            //event(new ApiRegistered($user));

            MobileConfirm::where('mobile', $mobile)->where('dialling_code', $request->dialling_code)
                ->delete();

            $code = OTP::generateOtp();
            $cnfrm_data = [
                'user_id' => $user->id,
                'dialling_code' => $request->dialling_code,
                'code' => $code,
                'mobile' => $mobile,
            ];

            if (MobileConfirm::create($cnfrm_data)) {

                $msg = __('رمز التحقق: :code', ['code' => $cnfrm_data['code']]);

                // (new MesgatService())->send_sms($mobile, $msg);

                return $this->sendResponse(['user' => $user, 'code' => $cnfrm_data['code']], __('mobile.You have registered successfully,please verify your mobile'));
            }

            return response()->json(['data' => $user, 'success' => true, 'message' => __('mobile.registered_successfully')], 200);
        // } catch (\Exception $e) {

        //     return $this->sendError($e->getMessage());
        // }
    }


    /**
     * @group User Registration
     *
     * send code to confirm mobile.
     * @bodyParam mobile string required the new mobile for the user.
     *
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
    public function sendMobileToken(MobileConfirmRequest $request)
    {

        $mobile = mobileHandler($request->mobile);
        if (isset(request()->guest)) {
            $user = User::where('mobile', $mobile)
                ->where('dialling_code', $request->dialling_code)
                ->first();

            if (!$user) {
                return $this->sendError(__('mobile.Mobile not found'));
            }
        }
        $code = OTP::generateOtp();
        if (isset($user)) {
            $data = array_merge(
                $request->validated(),
                ['code' => $code, 'user_id' => $user->id, 'mobile' => $mobile]
            );
        } else {
            $data = array_merge(
                $request->validated(),
                ['code' => $code, 'mobile' => $mobile]
            );
        }


        MobileConfirm::where('mobile', $mobile)->where('dialling_code', $request->dialling_code)
            ->delete();

        if (MobileConfirm::create($data)) {

            $msg = __('رمز التحقق: :code', ['code' => $data['code']]);

            // (new MesgatService())->send_sms($mobile, $msg);

            return $this->sendResponse(['code' => $data['code']], trans('passwords.sms_sent'));
        }

        return $this->sendError(__('mobile.something_wrong'));
    }

    public function checkMobileToken(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'dialling_code' => 'required',
                'mobile' => 'required',
                'code' => ['required', 'string'],
            ]);
    
            if ($validator->fails()) {
                // Increment failed attempts
                $this->incrementFailedAttempts($request->dialling_code, $request->mobile);
                return $this->sendError(__('mobile.wrong_data'));
            }
    
            // Normalize mobile number
            $mobile = mobileHandler($request->mobile);
    
            // Check if account is suspended
            $user = User::where('mobile', $mobile)->where('dialling_code', $request->dialling_code)->first();
            if ($user && $user->suspended_until && $user->suspended_until->isFuture()) {
                return $this->sendError(__('mobile.account_suspended_10_minutes'));
            }
    
            // Check failed attempts
            $attemptsKey = "mobile_verification_attempts:{$request->dialling_code}:{$mobile}";
            $attempts = Cache::get($attemptsKey, 0);
            if ($attempts > 3) {
                // Suspend account for 10 minutes
                if ($user) {
                    $user->update(['suspended_until' => now()->addMinutes(10)]);
                }
                Cache::forget($attemptsKey); // Clear attempts after suspension
                return $this->sendError(__('mobile.account_suspended_10_minutes'));
            }
    
            // Check verification code
            $record = MobileConfirm::where('code', $request->code)
                ->where('mobile', $mobile)
                ->where('dialling_code', $request->dialling_code)
                ->first();
    
            if ($record) {
                // Reset failed attempts on success
                Cache::forget($attemptsKey);
    
                // Update user
                if ($user = $record->user) {
                    $user->update([
                        'mobile' => $mobile,
                        'mobile_verified_at' => now(),
                    ]);
    
                    // Clear existing tokens
                    $user->deviceTokens()->delete();
                    $user->tokens()->delete();
    
                    // Generate new token
                    $expires = Carbon::now()->addDays(2);
                    $token = Auth::guard('api')->login($user);
    
                    return $this->sendResponse(
                        ['user' => $user, 'token' => $token, 'expires' => $expires],
                        __('Your mobile has been verified')
                    );
                }
            }
    
            // Increment failed attempts on incorrect code
            $this->incrementFailedAttempts($request->dialling_code, $request->mobile);
            return $this->sendError(__('mobile.you have provided wrong data'));
    
        } catch (\Exception $e) {
            return $this->sendError(__('mobile.something_wrong'));
        }
    }
    
    /**
     * Increment failed verification attempts in cache.
     *
     * @param string $dialling_code
     * @param string $mobile
     * @return void
     */
    protected function incrementFailedAttempts($dialling_code, $mobile)
    {
        $key = "mobile_verification_attempts:{$dialling_code}:{$mobile}";
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(10)); // Store for 10 minutes
    }
        /**
     * @group User Registration
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
     public function verifyAccountEmail(VerifyAccountRequest $request)
     {
 
         if (VerificationCode::verify($request->code, $request->email)) {
 
             $user = User::where('email', $request->email)->whereNull('email_verified_at')->first();
 
             $expires = Carbon::now()->addHours(5);
             $token = Auth::guard('api')->login($user);
             $user->setRememberToken($expires);
             $user->email_verified_at = now();
             $user->save();
     
             if (isset($request->device_token)) {
                 $user->deviceTokens()->firstOrCreate([
                     'device_token' => $request->device_token
                 ]);
             }
     
             event(new UserLoggedIn($user));
 
             return response()->json(['data' => [
                'user' => $user,
                'token' => $token,
                'expires' => $expires
            ], 'success' => true, 'message' => __('mobile.account_confirmed')], 200);

         }
 
         return $this->sendError(__('mobile.wrong_code'));
     }
 

    protected function create(array $data)
    {
        DB::beginTransaction();

        try {

            $data = array_merge($data, [
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);

            $user = User::create($data);

            // $user->assignRole('user');
            
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
            //throw new Exception(__('auth.something_wrong'));
        }

        DB::commit();

        return $user;
    }
}
