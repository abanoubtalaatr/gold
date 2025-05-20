<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderRental;
use App\Models\OrderSale;

class DashboardController extends Controller
{
    public function index()
    {
        // i want number of roles, number of admins, number of branches, number or rental request, number of sales order , number of rental order 
        $roles = Role::count();
        $admins = User::where('role', 'admin')->count();
        $branches = Branch::count();
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
