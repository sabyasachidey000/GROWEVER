<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\Employee;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::withCount('shiftAssignments')->paginate(25);
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:shifts',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'is_night_shift' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $shift = Shift::create($validated);

        $this->activityLog()->log(
            "Shift {$shift->name} created",
            $shift,
            'created',
            'shifts'
        );

        return redirect()->route('shifts.index')
            ->with('success', 'Shift created successfully.');
    }

    public function show(Shift $shift)
    {
        $shift->load(['shiftAssignments.employee', 'shiftAssignments.machine']);
        return view('shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:shifts,code,' . $shift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'is_night_shift' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $shift->update($validated);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }

    // Shift Assignments
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'machine_ids' => 'nullable|array',
            'machine_ids.*' => 'exists:machines,id',
            'assignment_date' => 'required|date',
        ]);

        foreach ($validated['employee_ids'] as $index => $employeeId) {
            ShiftAssignment::updateOrCreate(
                [
                    'shift_id' => $validated['shift_id'],
                    'employee_id' => $employeeId,
                    'assignment_date' => $validated['assignment_date'],
                ],
                [
                    'machine_id' => $validated['machine_ids'][$index] ?? null,
                ]
            );
        }

        return back()->with('success', 'Shift assignments created successfully.');
    }

    public function apiGetCurrentShift()
    {
        $currentShift = Shift::getCurrentShift();

        return response()->json($currentShift ? [
            'found' => true,
            'shift' => $currentShift,
        ] : ['found' => false]);
    }
    public function currentShift(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Kolkata');

            $currentTime = $now->format('H:i:s');

            // Only Shift A and Shift B
            $shifts = DB::table('shifts')
                ->whereIn('code', ['A', 'B'])
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get();

            $currentShift = null;

            foreach ($shifts as $shift) {

                $start = Carbon::createFromFormat(
                    'H:i:s',
                    $shift->start_time
                );

                $end = Carbon::createFromFormat(
                    'H:i:s',
                    $shift->end_time
                );

                $current = Carbon::createFromFormat(
                    'H:i:s',
                    $currentTime
                );

                /*
                 * Normal Shift
                 * Example:
                 * 08:00 -> 20:00
                 */
                if ($start->lt($end)) {

                    if (
                        $current->gte($start) &&
                        $current->lt($end)
                    ) {
                        $currentShift = $shift;
                        break;
                    }
                }

                /*
                 * Night Shift
                 * Example:
                 * 20:00 -> 08:00
                 *
                 * 20:00 - 23:59
                 * OR
                 * 00:00 - 08:00
                 */
                else {

                    if (
                        $current->gte($start) ||
                        $current->lt($end)
                    ) {
                        $currentShift = $shift;
                        break;
                    }
                }
            }

            if (!$currentShift) {

                return response()->json([
                    'status' => false,
                    'message' => 'No active shift found.',
                    'current_time' => $currentTime
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Current shift fetched successfully.',

                'current_time' => $currentTime,

                'shift' => [
                    'id' => $currentShift->id,
                    'name' => $currentShift->name,
                    'code' => $currentShift->code,

                    'start_time' => $currentShift->start_time,
                    'end_time' => $currentShift->end_time,

                    'break_start' => $currentShift->break_start,
                    'break_end' => $currentShift->break_end,

                    'is_night_shift' => $currentShift->is_night_shift,
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unable to fetch current shift.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
