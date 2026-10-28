<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    public function dailyProduction(Request $request)
    {
        $date = $request->get('date', today());
        return response()->json($this->reportService->dailyProductionReport($date));
    }

    public function machineUtilization(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        return response()->json($this->reportService->machineUtilizationReport($startDate, $endDate));
    }

    public function breakdown(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        return response()->json($this->reportService->breakdownReport($startDate, $endDate));
    }

    public function operatorPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        return response()->json($this->reportService->operatorPerformanceReport($startDate, $endDate));
    }

    public function rejectionAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        return response()->json($this->reportService->rejectionAnalysisReport($startDate, $endDate));
    }

    public function oeeReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        return response()->json($this->reportService->oeeReport($startDate, $endDate));
    }
}
