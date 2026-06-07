<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Zone;
use App\Models\TariffRate;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_customers' => Customer::where('is_active', true)->count(),
            'total_zones'     => Zone::where('is_active', true)->count(),
            'total_tariffs'   => TariffRate::where('is_active', true)->count(),
            'total_officers'  => User::where('role', 'petugas')->where('is_active', true)->count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
