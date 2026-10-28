<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['dashboard', 'Dashboard', 'Overview', 'dashboard'],
            ['departments', 'Departments', 'Management', 'departments.index'],
            ['product_types', 'Product Types', 'Management', 'product-types.index'],
            ['products', 'Products', 'Management', 'products.index'],
            ['designations', 'Designations', 'Management', 'designations.index'],
            ['shifts', 'Shifts', 'Management', 'shifts.index'],
            ['employees', 'Employees', 'Management', 'employees.index'],
            ['rfid', 'RFID Management', 'Management', 'rfid.index'],
            ['machines', 'Machines', 'Machines', 'machines.index'],
            ['production', 'Production Tracking', 'Production', 'production.index'],
            ['breakdowns', 'Breakdowns', 'Production', 'breakdowns.index'],
            ['rejections', 'Rejections', 'Production', 'rejections.index'],
            ['rejection_analysis', 'Rejection Analysis', 'Production', 'rejections.analysis'],
            ['quality', 'Quality Inspection', 'Quality & OEE', 'quality.index'],
            ['approvals', 'Approvals', 'Quality & OEE', 'approvals.index'],
            ['pending_approvals', 'Pending Production Approvals', 'Quality & OEE', 'pending_approvals.index'],
            ['approved_approvals', 'Approved Production', 'Quality & OEE', 'approved_approvals.index'],
            ['oee', 'OEE', 'Quality & OEE', 'oee.index'],
            ['users', 'Users', 'Administration', 'users.index'],
            ['reports', 'Reports', 'Administration', 'reports.index'],
            ['activity_logs', 'Activity Logs', 'Administration', 'activity-logs.index'],
            ['notifications', 'Notifications', 'Administration', 'notifications.index'],
        ];

        foreach ($menus as $sortOrder => [$menuKey, $name, $section, $routeName]) {
            Menu::updateOrCreate(
                ['menu_key' => $menuKey],
                [
                    'name' => $name,
                    'section' => $section,
                    'route_name' => $routeName,
                    'is_active' => true,
                    'sort_order' => $sortOrder + 1,
                ]
            );
        }
    }
}
