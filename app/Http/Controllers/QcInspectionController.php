<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\Product;
use App\Models\QualityInspection;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QcInspectionController extends Controller
{
    public function index()
    {
        $inspections = QualityInspection::with(['machine', 'product', 'shift', 'inspector'])
            ->orderByDesc('inspection_date')
            ->orderByDesc('inspection_time')
            ->orderByDesc('id')
            ->paginate(20);

        return view('qc.index', compact('inspections'));
    }

    public function create()
    {
        $machines = Machine::active()->orderBy('machine_code')->get(['id', 'machine_code', 'name']);
        $shifts = Shift::active()->orderBy('start_time')->get();
        $currentShift = Shift::getCurrentShift(now());

        return view('qc.create', compact('machines', 'shifts', 'currentShift'));
    }

    /** Return the authoritative, current product assignment for a machine. */
    public function machineProduct(Machine $machine)
    {
        abort_unless($machine->is_active, 422, 'The selected machine is inactive.');

        $product = $this->assignedProduct($machine);

        if (! $product) {
            return response()->json([
                'product' => null,
                'message' => 'No active product is assigned to this machine. Assign a product before recording QC.',
            ], 422);
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'product_code' => $product->product_code,
                'name' => $product->name,
                'unit_of_measure' => $product->unit_of_measure,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inspection_date' => ['required', 'date'],
            'inspection_time' => ['required', 'date_format:H:i'],
            'shift_id' => ['required', Rule::exists('shifts', 'id')->where('is_active', true)->whereNull('deleted_at')],
            'machine_id' => ['required', 'exists:machines,id'],
            'product_id' => ['nullable', 'integer'],
            'inspection_type' => ['required', 'in:first_piece,in_process,final,patrol,receiving'],
            'thread_gauge_go' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'thread_gauge_no_go' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'concentricity_minor_dia' => ['nullable', 'string', 'max:150'],
            'emt_gauge_after_tapping' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'burr_on_face' => ['nullable', 'in:Present,Absent,AB,N/A'],
            'burr_on_thread' => ['nullable', 'in:Present,Absent,AB,N/A'],
            'thread_flat' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'tap_mark' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'offset_value' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'angularity' => ['nullable', 'in:Pass,Fail,AB,N/A'],
            'visual_defects' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:5000'],
            'sample_size' => ['required', 'integer', 'min:0'],
            'passed_quantity' => ['required', 'integer', 'min:0'],
            'failed_quantity' => ['required', 'integer', 'min:0'],
            'result' => ['required', 'in:pass,fail,conditional'],
        ]);

        $sampleSize = (int) $validated['sample_size'];
        $passedQuantity = (int) $validated['passed_quantity'];
        $failedQuantity = (int) $validated['failed_quantity'];

        if ($sampleSize !== $passedQuantity + $failedQuantity) {
            throw ValidationException::withMessages([
                'sample_size' => 'Sample quantity must equal passed quantity plus rejected quantity.',
            ]);
        }

        $inspector = Employee::active()
            ->whereKey($request->user()?->employee_id)
            ->first();

        if (! $inspector) {
            throw ValidationException::withMessages([
                'inspector' => 'Your login must be linked to an active employee profile before recording QC.',
            ]);
        }

        try {
            $inspection = DB::transaction(function () use ($validated, $sampleSize, $passedQuantity, $failedQuantity, $inspector) {
                $machine = Machine::active()->lockForUpdate()->find($validated['machine_id']);

                if (! $machine) {
                    throw ValidationException::withMessages([
                        'machine_id' => 'The selected machine is inactive or no longer available.',
                    ]);
                }

                $product = $this->assignedProduct($machine);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'machine_id' => 'This machine has no active product assignment. Assign a product before recording QC.',
                    ]);
                }

                if (isset($validated['product_id']) && (int) $validated['product_id'] !== $product->id) {
                    throw ValidationException::withMessages([
                        'machine_id' => 'The product assignment changed. Please select the machine again before saving.',
                    ]);
                }

                return QualityInspection::create([
                    'machine_id' => $machine->id,
                    'product_id' => $product->id,
                    'shift_id' => $validated['shift_id'],
                    'inspector_id' => $inspector->id,
                    'inspection_date' => $validated['inspection_date'],
                    'inspection_time' => $validated['inspection_time'],
                    'inspection_type' => $validated['inspection_type'],
                    'sample_size' => $sampleSize,
                    'passed_quantity' => $passedQuantity,
                    'failed_quantity' => $failedQuantity,
                    'acceptance_rate' => $sampleSize > 0
                        ? round(($passedQuantity / $sampleSize) * 100, 2)
                        : 0,
                    'measurements' => collect($validated)->only([
                        'thread_gauge_go', 'thread_gauge_no_go', 'concentricity_minor_dia',
                        'emt_gauge_after_tapping', 'burr_on_face', 'burr_on_thread', 'thread_flat',
                        'tap_mark', 'offset_value', 'angularity', 'visual_defects',
                    ])->all(),
                    'remarks' => $validated['remarks'] ?? null,
                    'result' => $validated['result'],
                    'production_signature' => $inspector->name,
                    'checked_by' => $inspector->name,
                ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Unable to save QC inspection.', [
                'machine_id' => $validated['machine_id'],
                'user_id' => $request->user()?->id,
                'exception' => $exception,
            ]);

            return back()->withInput()->with('error', 'Unable to save the QC inspection. Please try again.');
        }

        $this->activityLog()->log(
            "QC inspection #{$inspection->id} recorded for {$inspection->machine->machine_code}",
            $inspection,
            'created',
            'quality'
        );

        return redirect()->route('qc-inspections.index')
            ->with('success', 'QC inspection report saved successfully.');
    }

    private function assignedProduct(Machine $machine): ?Product
    {
        // Machine maintenance currently synchronizes one product in machine_product.
        // Ordering makes legacy multi-row assignments deterministic until they are corrected.
        return $machine->products()->active()->orderBy('machine_product.product_id')->first();
    }
}
