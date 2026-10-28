<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = config('app.admin_initial_password');

        if (! is_string($adminPassword) || trim($adminPassword) === '') {
            if (! User::where('email', 'admin@mes.com')->exists()) {
                throw new \RuntimeException('Set INITIAL_ADMIN_PASSWORD in .env before seeding the administrator account.');
            }

            $adminPassword = '';
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'view machines', 'create machines', 'edit machines', 'delete machines',
            'view products', 'create products', 'edit products', 'delete products',
            'view production', 'create production', 'edit production', 'delete production',
            'view breakdowns', 'create breakdowns', 'edit breakdowns', 'close breakdowns',
            'view rejections', 'create rejections', 'edit rejections',
            'view quality', 'create quality', 'edit quality',
            'view shifts', 'manage shifts',
            'view approvals', 'approve production', 'reject production',
            'view reports', 'export reports',
            'view dashboard',
            'manage settings', 'view activity logs',
            'view oee',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $plantManager = Role::firstOrCreate(['name' => 'Plant Manager']);
        $plantManager->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view users', 'view employees', 'view machines',
            'view products', 'view production', 'view breakdowns', 'view rejections',
            'view quality', 'view shifts', 'manage shifts', 'view approvals',
            'approve production', 'reject production', 'view reports', 'export reports',
            'view oee', 'view activity logs',
        ])->get());

        $productionManager = Role::firstOrCreate(['name' => 'Production Manager']);
        $productionManager->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view employees', 'view machines', 'view products',
            'view production', 'create production', 'edit production', 'view breakdowns',
            'create breakdowns', 'view rejections', 'create rejections', 'view quality',
            'view shifts', 'manage shifts', 'view approvals', 'approve production',
            'reject production', 'view reports', 'export reports', 'view oee',
        ])->get());

        $maintenanceManager = Role::firstOrCreate(['name' => 'Maintenance Manager']);
        $maintenanceManager->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view machines', 'view breakdowns', 'create breakdowns',
            'edit breakdowns', 'close breakdowns', 'view employees', 'view reports',
            'export reports',
        ])->get());

        $shiftSupervisor = Role::firstOrCreate(['name' => 'Shift Supervisor']);
        $shiftSupervisor->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view employees', 'view machines', 'view products',
            'view production', 'create production', 'edit production', 'view breakdowns',
            'create breakdowns', 'view rejections', 'create rejections', 'view quality',
            'create quality', 'view shifts', 'view approvals', 'approve production',
            'reject production', 'view reports', 'export reports', 'view oee',
        ])->get());

        $operator = Role::firstOrCreate(['name' => 'Operator']);
        $operator->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view production', 'create production', 'view machines',
            'view breakdowns', 'create breakdowns', 'view rejections', 'create rejections',
        ])->get());

        $qualityInspector = Role::firstOrCreate(['name' => 'Quality Inspector']);
        $qualityInspector->syncPermissions(Permission::whereIn('name', [
            'view dashboard', 'view quality', 'create quality', 'edit quality',
            'view production', 'view rejections', 'create rejections', 'view reports',
            'export reports',
        ])->get());

        // Create Designations
        $designations = [
            ['code' => 'SA', 'name' => 'Super Admin', 'level' => 'executive'],
            ['code' => 'PM', 'name' => 'Plant Manager', 'level' => 'executive'],
            ['code' => 'PRM', 'name' => 'Production Manager', 'level' => 'manager'],
            ['code' => 'MM', 'name' => 'Maintenance Manager', 'level' => 'manager'],
            ['code' => 'SS', 'name' => 'Shift Supervisor', 'level' => 'supervisor'],
            ['code' => 'OP', 'name' => 'Operator', 'level' => 'staff'],
            ['code' => 'QI', 'name' => 'Quality Inspector', 'level' => 'staff'],
        ];

        foreach ($designations as $des) {
            Designation::firstOrCreate(['code' => $des['code']], $des);
        }

        // Create Super Admin user (skip if already exists)
        if (User::where('email', 'admin@mes.com')->exists()) {
            return;
        }

        $adminDept = Department::where('code', 'ADMIN')->first();
        $adminDesignation = Designation::where('code', 'SA')->first();

        $employee = Employee::firstOrCreate(
            ['employee_code' => 'EMP001'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@mes.com',
                'mobile' => '0000000000',
                'department_id' => $adminDept?->id,
                'designation_id' => $adminDesignation?->id,
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'admin@mes.com'],
            [
                'employee_id' => $employee->id,
                'name' => 'System Administrator',
                'password' => Hash::make($adminPassword),
                'status' => 'active',
            ]
        );

        $user->assignRole('Super Admin');
    }
}
