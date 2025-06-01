<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderSale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(OrderSale $order)
    {
        // dd($order);
        $order = $order->load('goldPiece');

        return view('invoice.index', compact('order'));
    }
}
