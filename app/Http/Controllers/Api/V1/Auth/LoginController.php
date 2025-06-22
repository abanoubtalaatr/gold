<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;
use Carbon\Carbon;
use App\Helpers\OTP;
use App\Models\User;
use App\Services\SMS\Msegat;
use Illuminate\Http\Request;
use App\Models\CanceledOrder;
use App\Models\MobileConfirm;
use App\Models\SystemSetting;
use App\Traits\ApiResponseTrait;
use App\Events\User\UserLoggedIn;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MesgatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\AppBaseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

/**
 * Class LoginController.
 */
class LoginController extends AppBaseController
{

    use ApiResponseTrait;

    protected $smsService;

    /**
     * RegisterController constructor.
     */
    public function __construct(Msegat $sms)
    {
        $this->smsService = $sms;
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {

        $messages = [
            'password.required' => __('Password cannot be empty'),
            'mobile.required' => __('Mobile number cannot be empty'),
            'dialling_code.required' => __('Dialling code cannot be empty'),
            'mobile.regex' => __('Mobile number must be a number'),
            'mobile.min' => __('Mobile number must be at least 9 digits'),
            'mobile.max' => __('Mobile number must be at most 9 digits'),
        ];

        $request->validate([
            'password' => ['required', 'string'],
            'mobile' => ['required', 'string', 'regex:/[0-9]/', 'min:9', 'max:9'],
            'dialling_code' => ['required', 'string'],
            "device_token" => "nullable|string|max:1000|min:1",
        ], $messages);
    }

    protected function validateSocialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_provider' => 'required|in:apple,google,facebook',
            'device_token' => 'nullable',
            'email' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $jsonResponse = response()->json(['errors' => $validator->errors()], 422);

            throw new HttpResponseException($jsonResponse);
        }
    }

    /**
     * @group User Login
     *
     */

    public function login(Request $request, HasherContract $hasher)
    {
        try {

            $this->validateLogin($request);

            $user = User::where('mobile', $request->mobile)
                ->where('dialling_code', $request->dialling_code)
                ->first();

            if ($user && $hasher->check($request->password, $user->getAuthPassword())) {

                $canceledOrder = CanceledOrder::where('user_id', $user->id)->first();
            
                if($canceledOrder->count >= SystemSetting::first()->max_canceled_orders){
                    return $this->errorResponse(__("mobile.account_suspended_because_you_exceeded_the_maximum_number_of_canceled_orders_contact_support"), [], 422);
                }
            
                $user->deviceTokens()->delete();
                $user->tokens()->delete();
                $expires = Carbon::now()->addHours(5);
                $token = Auth::guard('api')->login($user);
                // $token = $user->createToken('user_' . $user->email, ['*'], $expires)->plainTextToken;
                $user->setRememberToken($expires);
                $user->save();

                return $this->authenticated($request, $user, $token, $expires);
            }
        } catch (Exception $e) {

            return $this->errorResponse($e->getMessage(), 200);
        }
        // return $this->errorResponse($e->getMessage(), 422);
        return $this->errorResponse(trans('mobile.failed'));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        //return Str::lower($request->input($this->username())).'|'.$request->ip();

        return $request->ip();
    }


    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws GeneralException
     */
    protected function authenticated(Request $request, $user, $token = null, $expires = null)
    {

        // Check to see if the users account is confirmed and active
        if (null !== $user->email && !$user->isVerified()) {
            $user->sendEmailVerificationNotification();
            return $this->errorResponse(__('mobile.not_verified'), ['email' => $user->email, 'message' => __('mobile.We have sent a confirmation email.')], 200);
        }

        if (!$user->isMobileVerified()) {

            MobileConfirm::where('mobile', $user->mobile)->where('dialling_code', $user->dialling_code)
                ->delete();

            $cnfrm_data = [
                'user_id' => $user->id,
                'dialling_code' => $user->dialling_code,
                'code' => OTP::generateOtp(),
                'mobile' => $user->mobile,
            ];

            if (MobileConfirm::create($cnfrm_data)) {

                $msg = __('رمز التحقق: :code', ['code' => $cnfrm_data['code']]);

                // (new MesgatService())->send_sms($user->mobile, $msg);

                return $this->errorResponse('mobile.mobile_not_verified', ['code' => $cnfrm_data['code'],'is_verified' => false], 200);
            }
        }


        if (!$user->isActive()) {
            return $this->errorResponse('not_active', ['email' => $user->email, 'message' => __('mobile.your account has been deactivated.')], 200);
        }

        if (isset($request->device_token)) {
            $user->deviceTokens()->firstOrCreate([
                'device_token' => $request->device_token
            ]);
        }

        if (null !== $request->header('Guest-Token')) {
            $user->deviceTokens()->firstOrCreate([
                'guest_token' => $request->header('Guest-Token')
            ]);
        }

        event(new UserLoggedIn($user));

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'expires' => $expires
        ], __('mobile.You have logged in successfully.'));
    }

    /**
     * @group User Login
     *
     * User Logout
     *
     * Requires Authorization by bearer token
     *
     */
    public function logout()
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();

            $user->deviceTokens()->delete();
            $user->tokens()->delete();

            Auth::guard('api')->logout();
            return $this->successResponse([], __('mobile.logout_success'));
        }

        return $this->errorResponse(__('mobile.unauthenticated'), 401);
    }

    /**
     * @param $provider
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function social_login(Request $request)
    {
        $this->validateSocialLogin($request);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            DB::beginTransaction();

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'is_active' => 1,
                    'social_provider' => $request->social_provider,
                ]);
            } catch (Exception $e) {
                DB::rollBack();

                throw new Exception(__('mobile.There was a problem connecting to :provider', ['provider' => $request->social_provider]));
            }

            DB::commit();
        }

        if (null !== $user->email && !$user->isVerified()) {
            //implements ShouldQueue
            $user->sendEmailVerificationNotification();
            return $this->errorResponse('mobile.not_verified', ['email' => $user->email, 'message' => __('mobile.We have sent a confirmation email.')], 200);
        }

        if (!$user->isActive()) {
            return $this->errorResponse('mobile.not_active', ['email' => $user->email, 'message' => __('mobile.your account has been deactivated.')], 200);
        }

        $expires = Carbon::now()->addHours(5);

        $token = Auth::guard('api')->login($user);
        //$token = $user->createToken(request()->input('email'), $expires)->plainTextToken;
        $user->setRememberToken($expires);
        $user->save();

        if (isset($request->device_token)) {
            $user->deviceTokens()->firstOrCreate([
                'device_token' => $request->device_token
            ]);
        }


        event(new UserLoggedIn($user));

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token,
                'expires' => $expires
            ],
            'success' => true,
            'message' => __('mobile.You have logged in successfully.')
        ], 200);
    }


    public function broadcast_auth(Request $req)
    {

        $socketId = $req->input('socket_id');
        $channelName = $req->input('channel_name');

        $stringToAuth = $socketId . ':' . $channelName;
        $hashed = hash_hmac('sha256', $stringToAuth, env('REVERB_APP_SECRET'));

        return new JsonResponse(['auth' => env('REVERB_APP_KEY') . ':' . $hashed]);
    }
}