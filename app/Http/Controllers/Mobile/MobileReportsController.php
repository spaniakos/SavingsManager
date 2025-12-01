<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileReportsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $reportData = null;

        // Default values
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $breakdownType = $request->get('breakdown_type', 'super_category');

        // Generate report if dates are provided
        if ($request->has('generate')) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $reportService = app(ReportService::class);
            $reportData = $reportService->generateComprehensiveReport($user, $startDate, $endDate, $breakdownType);
        }

        return view('mobile.reports', [
            'reportData' => $reportData,
            'startDate' => is_string($startDate) ? $startDate : $startDate->format('Y-m-d'),
            'endDate' => is_string($endDate) ? $endDate : $endDate->format('Y-m-d'),
            'breakdownType' => $breakdownType,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));
        $breakdownType = $request->get('breakdown_type', 'super_category');

        $reportService = app(ReportService::class);
        $reportData = $reportService->generateComprehensiveReport($user, $startDate, $endDate, $breakdownType);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.reports.comprehensive-pdf', [
                'reportData' => $reportData,
                'startDate' => $startDate->format('d/m/Y'),
                'endDate' => $endDate->format('d/m/Y'),
                'breakdownType' => $breakdownType,
                'user' => $user,
            ]);

            // Configure PDF options to enable remote images (for emoji support)
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('isHtml5ParserEnabled', true);

            $filename = 'report_'.$startDate->format('Y-m-d').'_to_'.$endDate->format('Y-m-d').'.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('mobile.reports.index')
                ->with('error', __('common.pdf_export_error').': '.$e->getMessage());
        }
    }
}
