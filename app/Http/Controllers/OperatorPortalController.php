<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FaceAttendanceLog;
use App\Models\MachineBreakdown;
use App\Models\OperatorSession;
use App\Models\ProductionRun;
use App\Models\Product;
use App\Services\FaceRecognitionService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Http;
use phpseclib3\Net\SSH2;

class OperatorPortalController extends Controller
{
    public function __construct(
        protected FaceRecognitionService $faceRecognitionService,
        protected NotificationService $notificationService
    ) {}

    public function login()
    {
        return view('operator.login');
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'descriptor' => 'required|array|size:128',
            'descriptor.*' => 'numeric',
        ]);

        $result = $this->faceRecognitionService->loginOperator($validated['descriptor']);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        session([
            'operator_employee_id' => $result['data']['employee']->id,
            'operator_session_id' => $result['data']['session']->id,
            'operator_session_machine_ip' => $result['data']['machine']->machine_ip,
        ]);
       // dd($result['data']['machine']->machine_ip);
        /* Http::post('http://192.168.1.122:5000/relay',[
            'status'=>'ON'
        ]);*/
       /* dd(
    request()->ip(),
    request()->ips(),
    $_SERVER['REMOTE_ADDR'],
    request()->header('X-Forwarded-For')
);*/
        //if(request()->ip() == $result['data']['machine']->machine_ip){
        /*$ssh = new SSH2($result['data']['machine']->machine_ip);

        if (!$ssh->login('dipl', 'dipl1234')) {
            die('Login failed');
        }


        $output=$ssh->exec('pinctrl set 22 op && pinctrl set 22 dh');*/
        //dd($output);
        //}
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'redirect' => route('operator.dashboard'),
        ]);
    }

    public function dashboard(Request $request)
    {
        $context = $this->operatorContext();

        if (!$context) {
            return redirect()->route('operator.login')
                ->with('error', 'Please login with face recognition.');
        }

        return view('operator.dashboard', $this->buildDashboardData($context));
    }

    public function productionEntry()
    {
        $context = $this->operatorContext();

        if (!$context) {
            return redirect()->route('operator.login')
                ->with('error', 'Please login with face recognition.');
        }

        return view('operator.production-entry', $this->buildDashboardData($context));
    }

    public function breakdownEntry()
    {
        $context = $this->operatorContext();

        if (!$context) {
            return redirect()->route('operator.login')
                ->with('error', 'Please login with face recognition.');
        }

        return view('operator.breakdown-entry', $this->buildDashboardData($context));
    }

    public function storeBreakdown(Request $request)
    {
        $context = $this->operatorContext();

        if (!$context) {
            return redirect()->route('operator.login')
                ->with('error', 'Please login with face recognition.');
        }

        $validated = $request->validate([
            'breakdown_type' => 'required|in:mechanical,electrical,hydraulic,pneumatic,tool_failure,material_shortage,software,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:2000',
        ]);

        $session = $context['session'];
        $existingBreakdown = MachineBreakdown::where('machine_id', $session->machine_id)
            ->whereIn('status', ['open', 'in_progress', 'resolved'])
            ->latest('start_time')
            ->first();

        if ($existingBreakdown) {
            return redirect()->route('operator.breakdown-entry')
                ->with('error', 'A breakdown is already active for this machine. Please check the status panel.');
        }

        $breakdown = MachineBreakdown::create([
            'machine_id' => $session->machine_id,
            'reported_by' => $session->employee_id,
            'breakdown_type' => $validated['breakdown_type'],
            'severity' => $validated['severity'],
            'description' => $validated['description'],
            'start_time' => now(),
            'status' => 'open',
        ]);

        $breakdown->machine->update(['status' => 'breakdown']);

        $this->notificationService->notifyMachineBreakdown(
            $breakdown->machine_id,
            $breakdown->breakdown_type,
            $breakdown->machine->name
        );

        $this->activityLog()->log(
            "Operator {$session->employee->name} reported breakdown on {$breakdown->machine->name}",
            $breakdown,
            'operator_breakdown_reported',
            'operator_portal'
        );

        return redirect()->route('operator.dashboard')
            ->with('success', 'Breakdown reported successfully. Maintenance has been notified.');
    }

    public function storeProduction(Request $request)
    {
        $context = $this->operatorContext();

        if (!$context) {
            return redirect()->route('operator.login')
                ->with('error', 'Please login with face recognition.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'weight' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $session = $context['session'];

        $productionRun = ProductionRun::create([
            'machine_id' => $session->machine_id,
            'operator_id' => $session->employee_id,
            'product_id' => $validated['product_id'],
            'shift_id' => $session->shift_id,
            'operator_session_id' => $session->id,
            'start_time' => now(),
            'end_time' => now(),
            'runtime_minutes' => 0,
            'weight' => $validated['weight'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'completed',
        ]);

        $this->activityLog()->log(
            "Operator {$session->employee->name} entered production weight {$productionRun->weight} kg",
            $productionRun,
            'operator_weight_entry',
            'operator_portal'
        );

        return redirect()->route('operator.dashboard')
            ->with('success', 'Production weight saved successfully.');
    }

    public function logout()
    {
        $context = $this->operatorContext();

        if ($context) {
            $session = $context['session'];
            $session->logout();
            if (!$session->machine->activeBreakdown()->exists()) {
                $session->machine->update(['status' => 'idle']);
            }
            //if(request()->ip() == $session->machine->machine_ip){
             $ssh = new SSH2($session->machine->machine_ip);
           // $ssh = new SSH2('192.168.1.115');

            if (!$ssh->login('dipl', 'dipl1234')) {
                die('Login failed');
            }

            $output=$ssh->exec('python3 /home/dipl/gpio_controller/relay_on.py off');
           // }
            FaceAttendanceLog::create([
                'employee_id' => $session->employee_id,
                'machine_id' => $session->machine_id,
                'action' => 'operator_logout',
                'ip_address' => request()?->ip(),
            ]);
        }

        session()->forget(['operator_employee_id', 'operator_session_id']);

        return redirect()->route('operator.login')
            ->with('success', 'Logged out successfully.');
    }

    private function operatorContext(): ?array
    {
        $employeeId = session('operator_employee_id');
        $sessionId = session('operator_session_id');

        if (!$employeeId || !$sessionId) {
            return null;
        }

        $session = OperatorSession::with(['employee', 'machine', 'shift'])
            ->where('id', $sessionId)
            ->where('employee_id', $employeeId)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            session()->forget(['operator_employee_id', 'operator_session_id']);
            return null;
        }

        return [
            'employee' => $session->employee,
            'session' => $session,
        ];
    }

    private function buildDashboardData(array $context): array
    {
        $employee = $context['employee'];
        $activeSession = $context['session'];
        $machine = $activeSession->machine;
        $products = $machine->products()->active()->orderBy('product_code')->get();

        if ($products->isEmpty()) {
            $products = Product::active()->orderBy('product_code')->get();
        }

        $attendanceLogs = FaceAttendanceLog::with('machine')
            ->where('employee_id', $employee->id)
            ->latest()
            ->limit(15)
            ->get();

        $productionQuery = ProductionRun::with(['product', 'machine', 'shift'])
            ->where('operator_id', $employee->id)
            ->where('start_time', '>=', now()->subMonth())
            ->orderByDesc('start_time');

        $monthlyTotalWeight = (clone $productionQuery)->sum('weight');
        $productionRuns = $productionQuery->paginate(15);
        $breakdowns = MachineBreakdown::with(['machine', 'assignedTo'])
            ->where('reported_by', $employee->id)
            ->where('machine_id', $machine->id)
            ->latest('start_time')
            ->limit(10)
            ->get();
        $completedBreakdown = MachineBreakdown::with('machine')
            ->where('reported_by', $employee->id)
            ->where('machine_id', $machine->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->where('updated_at', '>=', now()->subDay())
            ->latest('updated_at')
            ->first();

        return compact(
            'employee',
            'activeSession',
            'machine',
            'products',
            'attendanceLogs',
            'productionRuns',
            'monthlyTotalWeight',
            'breakdowns',
            'completedBreakdown'
        );
    }
}
