<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'designation', 'user'])
            ->orderBy('employee_code')
            ->paginate(25);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        $designations = Designation::active()->get();
        $rfidCards = $this->registeredRfidCards();
        $rfidAssignments = $this->rfidAssignments();

        return view('employees.create', compact('departments', 'designations', 'rfidCards', 'rfidAssignments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees',
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|unique:employees',
            'mobile' => 'nullable|string|max:20',
            'rfid_card_number' => 'nullable|string|max:100|unique:employees',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'emergency_contact' => 'nullable|string|max:150',
            'emergency_phone' => 'nullable|string|max:20',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'address' => 'nullable|string',
        ]);

        $employee = Employee::create($validated);

        $this->activityLog()->log(
            "Employee {$employee->name} ({$employee->employee_code}) created",
            $employee,
            'created',
            'employees'
        );

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation', 'user', 'operatorSessions', 'productionRuns']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::active()->get();
        $designations = Designation::active()->get();
        $rfidCards = $this->registeredRfidCards();
        $rfidAssignments = $this->rfidAssignments($employee->id);

        return view('employees.edit', compact('employee', 'departments', 'designations', 'rfidCards', 'rfidAssignments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'mobile' => 'nullable|string|max:20',
            'rfid_card_number' => 'nullable|string|max:100|unique:employees,rfid_card_number,' . $employee->id,
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'emergency_contact' => 'nullable|string|max:150',
            'emergency_phone' => 'nullable|string|max:20',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'address' => 'nullable|string',
        ]);

        $employee->update($validated);

        $this->activityLog()->log(
            "Employee {$employee->name} updated",
            $employee,
            'updated',
            'employees'
        );

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        $this->activityLog()->log(
            "Employee {$employee->name} deleted",
            $employee,
            'deleted',
            'employees'
        );

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $employees = Employee::where('name', 'like', "%{$query}%")
            ->orWhere('employee_code', 'like', "%{$query}%")
            ->orWhere('rfid_card_number', 'like', "%{$query}%")
            ->active()
            ->limit(20)
            ->get(['id', 'employee_code', 'name', 'rfid_card_number']);

        return response()->json($employees);
    }

    public function assignRfid(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'rfid_card_number' => 'required|string|max:100|unique:employees,rfid_card_number,' . $employee->id,
        ]);

        $employee->update($validated);

        $this->activityLog()->log(
            "RFID card assigned to {$employee->name}",
            $employee,
            'rfid_assigned',
            'employees'
        );

        return response()->json(['message' => 'RFID card assigned successfully.']);
    }

    /**
     * Registered cards which are not already tagged to a different operator.
     * The RFID device registration remains the source of truth for cards.
     */
    private function registeredRfidCards()
    {
        if (!Schema::hasTable('rfid_register')) {
            return collect();
        }

        $cardColumn = collect(['card', 'uid', 'rfid', 'rfid_card_number', 'card_number'])
            ->first(fn ($column) => Schema::hasColumn('rfid_register', $column));

        if (!$cardColumn) {
            return collect();
        }

        return DB::table('rfid_register')
            ->whereNotNull($cardColumn)
            ->orderBy($cardColumn)
            ->pluck($cardColumn)
            ->map(fn ($card) => strtoupper(trim($card)))
            ->filter()
            ->unique()
            ->values();
    }

    /** Cards already reserved by an operator other than the one being edited. */
    private function rfidAssignments(?int $exceptEmployeeId = null)
    {
        return Employee::query()
            ->when($exceptEmployeeId, fn ($query) => $query->where('id', '!=', $exceptEmployeeId))
            ->whereNotNull('rfid_card_number')
            ->orderBy('name')
            ->get(['name', 'rfid_card_number'])
            ->mapWithKeys(fn ($employee) => [strtoupper(trim($employee->rfid_card_number)) => $employee->name]);
    }

    // API Methods
    public function apiIndex(Request $request)
    {
        $query = Employee::with(['department', 'designation']);

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('employee_code')->paginate(25));
    }

    public function apiShow(Employee $employee)
    {
        return response()->json($employee->load(['department', 'designation']));
    }
}
