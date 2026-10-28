<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\ModbusWeightReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModbusController extends Controller
{
    /**
     * Store a weight reading from the Modbus PLC.
     *
     * POST /api/production
     * {
     *     "machine_id": 1,
     *     "weight": 2500
     * }
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'machine_id' => ['required', 'integer', 'exists:machines,id'],
                'weight'     => ['required', 'integer', 'min:0'],
            ]);

            $reading = ModbusWeightReading::create([
                'machine_id' => $validated['machine_id'],
                'weight'     => $validated['weight'],
                'source'     => 'modbus_tcp',
                'plc_ip'     => $request->ip(),
            ]);

            // Update machine status to running when data arrives
            $machine = Machine::find($validated['machine_id']);
            if ($machine && in_array($machine->status, ['idle', 'offline'])) {
                $machine->update(['status' => 'running']);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Weight reading recorded',
                'data'    => [
                    'id'         => $reading->id,
                    'machine_id' => $reading->machine_id,
                    'weight'     => $reading->weight,
                    'created_at' => $reading->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }
}
