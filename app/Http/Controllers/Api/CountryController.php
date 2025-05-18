<?php

namespace App\Http\Controllers\Api;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SimpleStateResource;
use App\Http\Resources\Api\SimpleCountryResource;

class CountryController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $countries = Country::where('is_active',1)->get();
        
        return $this->successResponse(SimpleCountryResource::collection($countries));
    }

    public function states(Country $country)
    {
        $states = State::where('country_id', $country->id)->get();
        
        return $this->successResponse(SimpleStateResource::collection($states));
    }
}
