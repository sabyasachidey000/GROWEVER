<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::firstOrCreate(
            ['code' => 'ADMIN'],
            ['name' => 'Administration', 'description' => 'Administrative operations']
        );

        $designation = Designation::firstOrCreate(
            ['code' => 'SA'],
            ['name' => 'Super Admin', 'level' => 'executive', 'is_active' => true]
        );

        $employee = Employee::firstOrCreate(
            ['employee_code' => 'SUPERADMIN001'],
            [
                'name' => 'MTS Super Administrator',
                'email' => 'superadmin@mes.com',
                'mobile' => '0000000000',
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'superadmin@mes.com'],
            [
                'employee_id' => $employee->id,
                'name' => 'MTS Super Administrator',
                'password' => Hash::make('SuperAdmin@123'),
                'status' => 'active',
            ]
        );

        Role::firstOrCreate(['name' => 'Super Admin']);
        $user->syncRoles(['Super Admin']);
    }
}
