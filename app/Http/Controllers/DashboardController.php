<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the general dashboard (warehouse / cashier / staff) or redirect admin.
     */
    public function index(): View|RedirectResponse
    {
        if (Auth::user()?->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $totalProducts = Product::count();
        $totalUsers = User::where('role', '!=', 'super_admin')->count();

        return view('dashboard.Warehouse.Dashboard', compact(
            'totalProducts',
            'totalUsers'
        ));
    }

    /**
     * Display the admin dashboard.
     */
    public function admin(): View
    {
        $totalProducts = Product::count();
        $totalUsers = User::where('role', '!=', 'super_admin')->count();

        return view('dashboard.Admin.Dashboard', compact(
            'totalProducts',
            'totalUsers'
        ));
    }

    public function users(): View
    {
        return view('dashboard.Admin.User');
    }
}
