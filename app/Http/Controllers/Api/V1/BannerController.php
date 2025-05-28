<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Models\HomeSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Traits\ApiResponseTrait;

class BannerController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $home_sliders = HomeSlider::query()
            ->where('is_active', true)
            ->latest()
            ->get();

        return $this->successResponse(
            $home_sliders,
            __('mobile.banners_fetched_success')
        );

    }
}
