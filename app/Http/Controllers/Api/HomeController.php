<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Resources\BannerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request): JsonResponse
    {

        //************banners***************************
        $banners = BannerResource::collection(
            Banner::active()
                ->orderBy('sort_order')
                ->get()
        );
        //************end banners***************************

        $data = [
            'banners' => $banners,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

}
