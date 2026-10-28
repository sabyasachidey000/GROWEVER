<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeRfidPunch;
use App\Models\Machine;
use App\Models\MachineBreakdown;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RfidPunchWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_punches_toggle_login_logout_and_dashboard_active_state(): void
    {
        config(['rfid.debounce_seconds' => 0]);
        $this->makePunchFixture();

        $this->postJson('/api/rfid/punch', ['device_id' => 'ESP32_001', 'uid' => 'aa-bb-cc'])
            ->assertCreated()->assertJsonPath('action', 'LOGIN');
        $this->assertDatabaseCount('employee_rfid_punches', 1);
        $this->assertSame(1, EmployeeRfidPunch::activeEmployeeCount());

        $this->postJson('/api/rfid/punch', ['device_id' => 'ESP32_001', 'uid' => 'aa-bb-cc'])
            ->assertCreated()->assertJsonPath('action', 'LOGOUT');
        $this->assertDatabaseCount('employee_rfid_punches', 2);
        $this->assertSame(0, EmployeeRfidPunch::activeEmployeeCount());

        $this->postJson('/api/rfid/punch', ['device_id' => 'ESP32_001', 'uid' => 'aa-bb-cc'])
            ->assertCreated()->assertJsonPath('action', 'LOGIN');
        $this->assertSame(1, EmployeeRfidPunch::activeEmployeeCount());
    }

    public function test_rapid_duplicate_scan_is_raw_logged_but_not_business_processed(): void
    {
        config(['rfid.debounce_seconds' => 30]);
        $this->makePunchFixture();

        $this->postJson('/api/rfid/punch', ['device_id' => 'ESP32_001', 'uid' => 'aa-bb-cc'])->assertCreated();
        $this->postJson('/api/rfid/punch', ['device_id' => 'ESP32_001', 'uid' => 'aa-bb-cc'])
            ->assertOk()->assertJsonPath('duplicate', true);

        $this->assertDatabaseCount('rfid_punch_logs', 2);
        $this->assertDatabaseCount('employee_rfid_punches', 1);
    }

    public function test_unmapped_device_is_rejected_without_creating_a_punch(): void
    {
        $this->makePunchFixture();

        $this->postJson('/api/rfid/punch', ['device_id' => 'UNKNOWN_DEVICE', 'uid' => 'aa-bb-cc'])
            ->assertUnprocessable()->assertJsonPath('reason', 'unknown_device');

        $this->assertDatabaseCount('employee_rfid_punches', 0);
    }

    public function test_mapped_breakdown_button_creates_one_open_record_and_resolution_restores_idle(): void
    {
        foreach (['Maintenance Manager', 'Production Manager', 'Super Admin', 'Shift Supervisor'] as $role) {
            Role::create(['name' => $role]);
        }
        [$machine, $employee] = $this->makePunchFixture();

        $this->postJson('/api/rfid/button', ['device_id' => 'ESP32_001', 'reported_by' => $employee->id])
            ->assertCreated()->assertJsonPath('created', true);
        $this->assertDatabaseCount('machine_breakdowns', 1);
        $this->assertSame('breakdown', $machine->fresh()->status);

        $this->postJson('/api/rfid/button', ['device_id' => 'ESP32_001', 'reported_by' => $employee->id])
            ->assertOk()->assertJsonPath('created', false);
        $this->assertDatabaseCount('machine_breakdowns', 1);

        MachineBreakdown::firstOrFail()->close();
        $this->assertDatabaseHas('machine_breakdowns', ['status' => 'closed']);
        $this->assertSame('idle', $machine->fresh()->status);
    }

    private function makePunchFixture(): array
    {
        $machine = Machine::create([
            'machine_code' => 'M-001', 'name' => 'Test Machine', 'type' => 'CNC',
            'operation_type' => 'cnc', 'status' => 'idle', 'is_active' => true,
            'rfid_device_id' => 'ESP32_001',
        ]);

        $employee = Employee::create([
            'employee_code' => 'EMP001', 'name' => 'Test Employee',
            'rfid_card_number' => 'AA-BB-CC', 'status' => 'active',
        ]);

        return [$machine, $employee];
    }
}
