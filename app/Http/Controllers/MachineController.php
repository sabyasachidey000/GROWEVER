<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Product;
use App\Models\MachineBreakdown;
use App\Services\EnergyConsumptionService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected EnergyConsumptionService $energyConsumptionService
    ) {
        
    }

    public function index()
    {
        $machines = Machine::orderBy('machine_code')->paginate(25);
        $machineEnergySummaries = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->machineSummary($machine),
            ]);
        $machineEnergyReadings = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->recentReadings($machine, 10),
            ]);
        $machineEnergyCharts = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->chartData($machine, 30),
            ]);
        $machineEnergyCards = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->latestMetricCards($machine),
            ]);
        $machineEnergyFieldAnalysis = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->fieldAnalysis($machine),
            ]);
        $machineEnergyAlerts = $machines->getCollection()
            ->mapWithKeys(fn(Machine $machine) => [
                $machine->id => $this->energyConsumptionService->alerts($machine),
            ]);

        return view('machines.index', compact(
            'machines',
            'machineEnergySummaries',
            'machineEnergyReadings',
            'machineEnergyCharts',
            'machineEnergyCards',
            'machineEnergyFieldAnalysis',
            'machineEnergyAlerts'
        ));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('machines.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_code' => 'required|string|max:50|unique:machines,machine_code',
            'name' => 'required|string|max:150',
            'type' => 'required|string|max:100',
            'operation_type' => 'required|in:cnc,manual,semi_auto,auto',
            'manufacturer' => 'nullable|string|max:150',
            'model_number' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'plc_model' => 'nullable|string|max:100',
            'plc_ip_address' => 'nullable|ip',
            'location' => 'nullable|string|max:150',
            'production_capacity_per_hour' => 'nullable|numeric',
            'production_capacity_per_day' => 'nullable|numeric',
            'specifications' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'status' => 'required|in:running,idle,breakdown,maintenance,offline',
            'is_active' => 'sometimes|boolean',
            'rfid_device_id' => 'nullable|string|max:100|unique:machines,rfid_device_id',
            'machine_ip' => 'nullable|ip',
            'cycle_count' => 'required|string|max:150',
    
            // Single product selection
            'product_id' => 'nullable|integer|exists:products,id',
        ]);
    
        // Create machine without product_id
        unset($validated['product_id']);
    
        $machine = Machine::create($validated);
    
        // Attach the selected product
        if ($request->filled('product_id')) {
            $machine->products()->sync([
                $request->input('product_id')
            ]);
        }
    
        $this->activityLog()->log(
            "Machine {$machine->name} ({$machine->machine_code}) registered",
            $machine,
            'created',
            'machines'
        );
    
        return redirect()->route('machines.index')
            ->with('success', 'Machine registered successfully.');
    }

    public function show(Machine $machine)
    {
        $machine->load([
            'productionRuns' => fn($q) => $q->latest()->limit(10),
            'breakdowns' => fn($q) => $q->latest()->limit(10),
        ]);

        $utilization = $machine->getUtilizationForDate(today());
        $activeSession = $machine->operatorSessions()->active()->latest()->first();

        return view('machines.show', compact('machine', 'utilization', 'activeSession'));
    }

    public function edit(Machine $machine)
    {
        $machine->load('products');
        $products = Product::active()->orderBy('name')->get();
        return view('machines.edit', compact('machine', 'products'));
    }

    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'machine_code' => 'required|string|max:50|unique:machines,machine_code,' . $machine->id,
            'name' => 'required|string|max:150',
            'type' => 'required|string|max:100',
            'operation_type' => 'required|in:cnc,manual,semi_auto,auto',
            'manufacturer' => 'nullable|string|max:150',
            'model_number' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'plc_model' => 'nullable|string|max:100',
            'plc_ip_address' => 'nullable|ip',
            'location' => 'nullable|string|max:150',
            'production_capacity_per_hour' => 'nullable|numeric',
            'production_capacity_per_day' => 'nullable|numeric',
            'specifications' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'status' => 'required|in:running,idle,breakdown,maintenance,offline',
            'is_active' => 'sometimes|boolean',
            'machine_ip' => 'nullable|string|max:45',
            'rfid_device_id' => 'nullable|string|max:100|unique:machines,rfid_device_id,' . $machine->id,
            'cycle_count' => 'required|string|max:150',
    
            // Single product selection
            'product_id' => 'nullable|integer|exists:products,id',
        ]);
    
        $machine->update($validated);
    
        // Synchronize the selected product
        $machine->products()->sync(
            !empty($validated['product_id'])
                ? [$validated['product_id']]
                : []
        );
    
        $this->activityLog()->log(
            "Machine {$machine->name} updated",
            $machine,
            'updated',
            'machines'
        );
    
        return redirect()->route('machines.index')
            ->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();

        $this->activityLog()->log(
            "Machine {$machine->name} deleted",
            $machine,
            'deleted',
            'machines'
        );

        return redirect()->route('machines.index')
            ->with('success', 'Machine deleted successfully.');
    }

    public function updateStatus(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'status' => 'required|in:running,idle,breakdown,maintenance,offline',
        ]);

        $machine->update($validated);

        if ($validated['status'] === 'breakdown') {
            $this->notificationService->notifyMachineBreakdown(
                $machine->id,
                'manual',
                $machine->name
            );
        }

        $this->activityLog()->log(
            "Machine {$machine->name} status changed to {$validated['status']}",
            $machine,
            'status_changed',
            'machines'
        );

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function reportBreakdown(Machine $machine)
    {
        $reporterId = auth()->user()?->employee_id;
        abort_unless($reporterId, 403, 'An employee profile is required to report a breakdown.');

        $breakdown = DB::transaction(function () use ($machine, $reporterId) {
            $machine = Machine::lockForUpdate()->findOrFail($machine->id);
            $existing = MachineBreakdown::where('machine_id', $machine->id)
                ->whereIn('status', ['open', 'in_progress', 'resolved'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $breakdown = MachineBreakdown::create([
                'machine_id' => $machine->id,
                'reported_by' => $reporterId,
                'breakdown_type' => 'other',
                'severity' => 'high',
                'description' => 'Breakdown reported from machine management.',
                'start_time' => now(),
                'status' => 'open',
            ]);

            $machine->update(['status' => 'breakdown']);

            return $breakdown;
        });

        if ($breakdown->wasRecentlyCreated) {
            $this->notificationService->notifyMachineBreakdown($machine->id, $breakdown->breakdown_type, $machine->name);
            $this->activityLog()->log("Breakdown reported on {$machine->name}", $breakdown, 'created', 'breakdowns');
            return back()->with('success', "Breakdown reported for {$machine->name}.");
        }

        return back()->with('error', "An open breakdown already exists for {$machine->name}.");
    }

    // API Methods
    public function apiIndex()
    {
        return response()->json(Machine::orderBy('machine_code')->paginate(25));
    }

    public function apiShow(Machine $machine)
    {
        return response()->json($machine);
    }

    public function apiStatus(Machine $machine)
    {
        return response()->json([
            'status' => $machine->status,
            'current_operator' => $machine->operatorSessions()->active()->latest()->first()?->employee?->name,
            'active_breakdown' => $machine->activeBreakdown?->breakdown_type,
        ]);
    }
    
}
