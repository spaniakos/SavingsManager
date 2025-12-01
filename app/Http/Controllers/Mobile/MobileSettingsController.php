<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileSettingsController extends Controller
{
    public function index()
    {
        return view('mobile.settings');
    }
}
