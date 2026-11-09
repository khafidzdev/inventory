<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan view 'dashboard' atau view inventory Anda sudah ada di resources/views/
        return view('dashboard.Dashboard'); 
    }
}