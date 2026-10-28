<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\OeeRecord;
use App\Services\OeeService;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class OeeController extends Controller
{
    public function __construct(
        protected OeeService $oeeService,
        protected DashboardService $dashboardService
    ) {
        
    }

    public function index(Request $request)
    {
        $machines = Machine::active()->get();
        $selectedMachine = $request->get('machine_id');
        $date = $request->get('date', today());

        $oeeSummary = $this->oeeService->getOeeSummary(
            $selectedMachine ? (int) $selectedMachine : null,
            $date,
            $date
        );

        $ranking = $this->oeeService->getMachineOeeRanking($date);

        $history = $selectedMachine
            ? $this->oeeService->getMachineOeeHistory((int) $selectedMachine, 30)
            : collect();

        return view('oee.index', compact('oeeSummary', 'ranking', 'history', 'machines', 'selectedMachine', 'date'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'date' => 'required|date',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $record = $this->oeeService->calculateOee(
            $validated['machine_id'],
            $validated['date'],
            $validated['shift_id']
        );

        return response()->json([
            'message' => 'OEE calculated successfully',
            'oee' => $record->getOverallOee(),
            'availability' => $record->getAvailability(),
            'performance' => $record->getPerformance(),
            'quality' => $record->getQuality(),
        ]);
    }

    public function calculateDaily()
    {
        $this->oeeService->calculateDailyOee();

        return back()->with('success', 'Daily OEE calculated for all machines and shifts.');
    }

    public function history(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $days = $request->integer('days', 30);
        $history = $this->oeeService->getMachineOeeHistory($validated['machine_id'], $days);

        return view('oee.history', compact('history', 'days'));
    }

    public function apiSummary(Request $request)
    {
        $summary = $this->oeeService->getOeeComparison(
            $request->get('start_date', now()->subDays(30)),
            $request->get('end_date', now())
        );

        return response()->json($summary);
    }

    public function apiRanking(Request $request)
    {
        $date = $request->get('date', today());
        $ranking = $this->oeeService->getMachineOeeRanking($date);

        return response()->json($ranking);
    }
}
