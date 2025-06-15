<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SimpleCityResource;

class StateController extends Controller
{
    use ApiResponseTrait;
    public function cities(State $state)
    {
        $country = Country::find(194);
        if (!$country) {
            return $this->errorResponse('Country not found', 404);
        }
        // i want to get all cities for this all states for this country  
        $states = State::where('country_id', $country->id)->pluck('id')->toArray();
        $cities = City::where('status',1)->get();
        
        return $this->successResponse(SimpleCityResource::collection($cities));
    }
}
