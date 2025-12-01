<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileReportsController extends Controller
{
    public function index()
    {
        return view('mobile.reports');
    }
}
