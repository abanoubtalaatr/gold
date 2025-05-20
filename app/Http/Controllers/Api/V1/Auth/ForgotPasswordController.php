<?php
namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Password;

/**
 * Class ForgotPasswordController.
 */
class ForgotPasswordController extends Controller
{
    use ApiResponseTrait;
    /**
     * @group User Login
     * Send a password reset code to the given user.
     *
     * @bodyParam email email required the email of the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetCodeEmail(Request $request)
    {

        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetCode(
            $this->credentials($request), null
        );
        return $response == 'passwords.sent_code'
        ? $this->sendResetCodeResponse($request, $response)
        : $this->sendResetCodeFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetCodeResponse(Request $request, $response)
    {
        $message = trans($response);

        if ($response === 'passwords.sent_code') {
            $message = trans('passwords.sent_code');
        }

        return $this->successResponse(
            [],
            $message
        );

        return response()->json(['success' => true, 'message' => $message], 200);

    }
    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetCodeFailedResponse(Request $request, $response)
    {

        return $this->successResponse(
            [],
            trans($response)
        );

        return response()->json(['data' => $request->only('email'), 'success' => false, 'message' => trans($response)], 400);

    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

     /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email','type'=>'required|in:user,company,employee']);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(['email','type']);
    }
}