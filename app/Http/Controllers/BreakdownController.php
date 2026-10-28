<?php

namespace App\Http\Controllers;

use App\Models\MachineBreakdown;
use App\Models\Machine;
use App\Models\Employee;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BreakdownController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
        
    }

    public function index(Request $request)
    {
        $query = MachineBreakdown::with(['machine', 'reportedBy', 'assignedTo'])
            ->orderByDesc('start_time');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        $breakdowns = $query->paginate(25);
        return view('breakdowns.index', compact('breakdowns'));
    }

    /*public function history(Request $request)
    {
        $query = MachineBreakdown::with(['machine']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // Summary stats per machine
        $machineStats = Machine::active()
            ->withCount(['breakdowns' => function ($q) use ($request) {
                if ($request->has('status')) {
                    $q->where('status', $request->status);
                }
                if ($request->has('date_from')) {
                    $q->whereDate('start_time', '>=', $request->date_from);
                }
                if ($request->has('date_to')) {
                    $q->whereDate('start_time', '<=', $request->date_to);
                }
            }])
            ->withSum(['breakdowns' => function ($q) use ($request) {
                if ($request->has('status')) {
                    $q->where('status', $request->status);
                }
                if ($request->has('date_from')) {
                    $q->whereDate('start_time', '>=', $request->date_from);
                }
                if ($request->has('date_to')) {
                    $q->whereDate('start_time', '<=', $request->date_to);
                }
            }], 'duration_minutes')
            ->orderByDesc('breakdowns_count')
            ->get();

        $breakdowns = $query->latest('start_time')->paginate(50);
        $machines = Machine::orderBy('machine_code')->get();

        return view('breakdowns.history', compact('machineStats', 'breakdowns', 'machines'));
    }*/
    
    public function history(Request $request)
    {
        $query = MachineBreakdown::with(['machine']);
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('machine_id')) {
            $query->forMachine($request->machine_id);
        }
    
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }
    
        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }
    
        // Summary stats per machine
        $machineStats = Machine::active()
            ->withCount(['breakdowns' => function ($q) use ($request) {
                if ($request->filled('status')) {
                    $q->where('status', $request->status);
                }
    
                if ($request->filled('date_from')) {
                    $q->whereDate('start_time', '>=', $request->date_from);
                }
    
                if ($request->filled('date_to')) {
                    $q->whereDate('start_time', '<=', $request->date_to);
                }
            }])
            ->withSum(['breakdowns' => function ($q) use ($request) {
                if ($request->filled('status')) {
                    $q->where('status', $request->status);
                }
    
                if ($request->filled('date_from')) {
                    $q->whereDate('start_time', '>=', $request->date_from);
                }
    
                if ($request->filled('date_to')) {
                    $q->whereDate('start_time', '<=', $request->date_to);
                }
            }], 'duration_minutes')
            ->orderByDesc('breakdowns_count')
            ->get();
    
        $breakdowns = $query->latest('start_time')->paginate(50);
    
        $machines = Machine::orderBy('machine_code')->get();
    
        return view(
            'breakdowns.history',
            compact('machineStats', 'breakdowns', 'machines')
        );
    }


    public function create()
    {
        $machines = Machine::active()->get();
        $employees = Employee::active()->get();
        return view('breakdowns.create', compact('machines', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'reported_by' => 'required|exists:employees,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'breakdown_type' => 'required|in:mechanical,electrical,hydraulic,pneumatic,tool_failure,material_shortage,software,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
        ]);

        $validated['start_time'] = now();
        $validated['status'] = 'open';

        $breakdown = MachineBreakdown::create($validated);

        // Update machine status
        $breakdown->machine->update(['status' => 'breakdown']);

        // Notify maintenance
        $this->notificationService->notifyMachineBreakdown(
            $breakdown->machine_id,
            $breakdown->breakdown_type,
            $breakdown->machine->name
        );

        $this->activityLog()->log(
            "Breakdown reported on {$breakdown->machine->name}: {$breakdown->breakdown_type}",
            $breakdown,
            'created',
            'breakdowns'
        );

        return redirect()->route('breakdowns.index')
            ->with('success', 'Breakdown reported successfully.');
    }

    public function show(MachineBreakdown $breakdown)
    {
        $breakdown->load(['machine', 'reportedBy', 'assignedTo']);
        return view('breakdowns.show', compact('breakdown'));
    }

    public function edit(MachineBreakdown $breakdown)
    {
        $machines = Machine::active()->get();
        $employees = Employee::active()->get();
        return view('breakdowns.edit', compact('breakdown', 'machines', 'employees'));
    }

    public function update(Request $request, MachineBreakdown $breakdown)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'breakdown_type' => 'required|in:mechanical,electrical,hydraulic,pneumatic,tool_failure,material_shortage,software,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
            'root_cause' => 'nullable|string',
            'repair_action' => 'nullable|string',
            'spare_parts_used' => 'nullable|string',
            'repair_cost' => 'nullable|numeric',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $breakdown->update($validated);

        if ($breakdown->status === 'in_progress' && !$breakdown->assigned_to) {
            $breakdown->update(['assigned_to' => auth()->user()->employee_id]);
        }

        $this->activityLog()->log(
            "Breakdown #{$breakdown->id} updated to status: {$breakdown->status}",
            $breakdown,
            'updated',
            'breakdowns'
        );

        return redirect()->route('breakdowns.index')
            ->with('success', 'Breakdown updated successfully.');
    }

    public function close(MachineBreakdown $breakdown)
    {
        $breakdown->close(
            request('repair_action'),
            request('root_cause')
        );

        $this->activityLog()->log(
            "Breakdown #{$breakdown->id} closed for {$breakdown->machine->name}",
            $breakdown,
            'closed',
            'breakdowns'
        );

        return back()->with('success', 'Breakdown closed successfully.');
    }

    public function destroy(MachineBreakdown $breakdown)
    {
        $breakdown->delete();

        $this->activityLog()->log(
            "Breakdown #{$breakdown->id} deleted",
            $breakdown,
            'deleted',
            'breakdowns'
        );

        return redirect()->route('breakdowns.index')
            ->with('success', 'Breakdown deleted successfully.');
    }

    public function apiIndex(Request $request)
    {
        $query = MachineBreakdown::with(['machine', 'reportedBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        return response()->json($query->latest()->paginate(25));
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'reported_by' => 'required|exists:employees,id',
            'breakdown_type' => 'required|in:mechanical,electrical,hydraulic,pneumatic,tool_failure,material_shortage,software,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
        ]);

        $validated['start_time'] = now();
        $validated['status'] = 'open';

        $breakdown = MachineBreakdown::create($validated);
        $breakdown->machine->update(['status' => 'breakdown']);

        return response()->json($breakdown, 201);
    }
    public function generateMemo($id)
    {
        $query = MachineBreakdown::where('id',$id)->first();
        $emp=Employee::where('id',$query->attened_by??0)->first();
        // Example: Database থেকে data আনবেন
        $memo = [
            'format_no'     => 'GEG/MAN/03B-A',
            'rev_no'        => $query->id,
            'rev_date'      => date('d-m-Y',strtotime($query->start_time)),

            'company'       => 'GROW EVER GROUP',
            'address'       => '8/1A, SHREE MANJARI BUILDING, SIR WILLIAM JONES SARANI, 5TH FLOOR, KOLKATA - 700071',

            'date'          => now()->format('d/m/Y'),
            'department'    => 'Maintenance - Mech. / Elec.',
            'shift'         => '',

            'machine_name'  => 'Tapping Machine',
            'machine_no'    => 'MCH-001',
            'time'          => date('d-m-Y H:i:s',strtotime($query->start_time)),

            'problem'       => $query->root_cause,

            'breakdown_reason' => $query->root_cause,

            'action_taken' => $query->repair_action,

            'repair_completion_time' => date('d-m-Y H:i:s',strtotime($query->end_time)),

            'total_down_time' => $query->duration_minutes.' Min',

            'attended_by' => $emp->name??'',

            'supervisor' => '',
        ];

        $pdf = Pdf::loadView(
            'pdf.inter-office-memo',
            compact('memo')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'inter-office-memo-' . $id . '.pdf'
        );
    }
}
