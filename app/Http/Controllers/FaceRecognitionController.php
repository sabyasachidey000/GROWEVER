<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FaceAttendanceLog;
use App\Models\Machine;
use App\Models\OperatorSession;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;

class FaceRecognitionController extends Controller
{
    public function __construct(
        protected FaceRecognitionService $faceRecognitionService
    ) {}

    public function index()
    {
        $employees = Employee::active()
            ->with(['department', 'designation', 'faceMachine'])
            ->orderBy('name')
            ->get();
        $machines = Machine::active()->orderBy('machine_code')->get();

        return view('face-recognition.index', compact('employees', 'machines'));
    }

    public function attendance()
    {
        $machines = Machine::with(['activeBreakdown'])
            ->orderBy('machine_code')
            ->get();

        $activeSessions = OperatorSession::with(['employee', 'machine', 'shift'])
            ->where('status', 'active')
            ->latest('login_time')
            ->get();

        $logs = FaceAttendanceLog::with(['employee', 'machine'])
            ->latest()
            ->paginate(50);

        return view('face-recognition.attendance', compact('machines', 'activeSessions', 'logs'));
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'machine_id' => 'required|exists:machines,id',
            'descriptors' => 'required|array|min:5',
            'descriptors.*' => 'required|array|size:128',
            'descriptors.*.*' => 'numeric',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $this->faceRecognitionService->enroll($employee, $validated['descriptors'], (int) $validated['machine_id']);

        return response()->json([
            'success' => true,
            'message' => "Face enrolled for {$employee->name} with multiple angles and expressions.",
        ]);
    }

    public function unassign(Employee $employee)
    {
        $this->faceRecognitionService->removeEnrollment($employee);

        return back()->with('success', "Face enrollment removed for {$employee->name}.");
    }

    public function processAttendance(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'action' => 'required|in:face_in,face_out',
            'descriptor' => 'required|array|size:128',
            'descriptor.*' => 'numeric',
        ]);

        $result = $this->faceRecognitionService->processAttendance(
            $validated['descriptor'],
            (int) $validated['machine_id'],
            $validated['action']
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
