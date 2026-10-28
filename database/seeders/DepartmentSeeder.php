<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'PROD', 'name' => 'Production', 'description' => 'Manufacturing operations'],
            ['code' => 'MAINT', 'name' => 'Maintenance', 'description' => 'Machine maintenance and repair'],
            ['code' => 'QUAL', 'name' => 'Quality', 'description' => 'Quality assurance and inspection'],
            ['code' => 'STORE', 'name' => 'Store', 'description' => 'Raw material and finished goods store'],
            ['code' => 'ADMIN', 'name' => 'Administration', 'description' => 'Administrative operations'],
            ['code' => 'ENG', 'name' => 'Engineering', 'description' => 'Process and product engineering'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
