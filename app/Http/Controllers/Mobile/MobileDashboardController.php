<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MobileDashboardController extends Controller
{
    public function index()
    {
        // The view handles all data fetching
        return view('mobile.dashboard');
    }
}
