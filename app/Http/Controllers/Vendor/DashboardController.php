<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        
        // i want number of roles, number of admins, number of branches, number or rental request, number of sales order , number of rental order 
        $roles = Role::where('vendor_id', Auth::id())->count();
        $admins = User::where('vendor_id', Auth::id())->whereHas('roles')->count();
        $branches = Branch::where('vendor_id', Auth::id())->count();
        $rentalRequests = OrderRental::count();
        $salesOrders = OrderSale::count();
        $rentalOrders = OrderRental::count();
        
        return Inertia::render('Vendor/Dashboard', [
            'roles' => $roles,
            'admins' => $admins,
            'branches' => $branches,
            'rentalRequests' => $rentalRequests,
            'salesOrders' => $salesOrders,
            'rentalOrders' => $rentalOrders,
        ]);
    }
}
