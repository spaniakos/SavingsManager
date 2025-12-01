<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;

class MobileDashboardController extends Controller
{
    public function index()
    {
        // The view handles all data fetching
        return view('mobile.dashboard');
    }
}
