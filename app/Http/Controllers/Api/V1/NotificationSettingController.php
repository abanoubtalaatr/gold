<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(Request $request)
    {
        $user = $request->user();

        $user->enable_notifications = !$user->enable_notifications;
        $user->save();

        return $this->successResponse([], __('mobile.notification setting updated successfully'));
    }
}
