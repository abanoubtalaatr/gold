<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {

        $UserPerRolechartData = $this->getRolesChartData();

        $statusChartData = $this->getUsersChartDataByStatus();

        return Inertia::render('Dashboard', [
            'UserPerRolechartData' => $UserPerRolechartData,
            'statusChartData' => $statusChartData,
            'userCount' => User::whereIsActive(1)->count(),
            'rolesCount' => Role::whereIsActive(1)->count(),

        ]);
    }


    private function getRolesChartData()
    {
        return Cache::remember('roles_chart_data', 60, function () {
            $roles = Role::withCount('users')->get();
            return [
                'labels' => $roles->pluck('name'),
                'datasets' => [
                    [
                        'label' => 'Users per Role',
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                        'data' => $roles->pluck('users_count'),
                    ],
                ],
            ];
        });
    }


    private function getUsersChartDataByStatus()
    {
        return Cache::remember('users_by_status_chart_data', 60, function () {
            $usersByStatus = User::select('is_active', \DB::raw('count(*) as total'))
                ->groupBy('is_active')
                ->get();

            return [
                'labels' => ['Inactive', 'Active'],
                'datasets' => [
                    [
                        'label' => 'Users by Status',
                        'backgroundColor' => ['#FF6384', '#36A2EB'],
                        'data' => [
                            $usersByStatus->where('is_active', 0)->first()->total ?? 0,
                            $usersByStatus->where('is_active', 1)->first()->total ?? 0,
                        ],
                    ],
                ],
            ];
        });
    }


}
