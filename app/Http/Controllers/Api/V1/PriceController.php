<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PriceRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
class PriceController extends Controller
{
    use ApiResponseTrait;
    public function index(PriceRequest $request)
    {
        $totalPrice = $this->getTotalPrice($request);
        return $this->successResponse($totalPrice);
    }

    public function getTotalPrice(PriceRequest $request)
    {
        //basic price this must calling api to get the basic price for today
        $arrayOfBasicPrice = [
            '21' => 100,
            '22' => 200,
            '24' => 300,
            '18' => 400,
        ];

        $basicPrice = $arrayOfBasicPrice[$request->carat];

        $totalPrice = $basicPrice * $request->weight;
        // if number_rental_day is not null, add the rental price to the total price
      
        if ($request->number_rental_day) {
            $totalPrice += $basicPrice * $request->number_rental_day;
        }

        
        return $totalPrice;
    }
}
