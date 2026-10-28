<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Shift A - Morning', 'code' => 'A', 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'break_start' => '10:00:00', 'break_end' => '10:30:00', 'is_night_shift' => false],
            ['name' => 'Shift B - Evening', 'code' => 'B', 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'break_start' => '18:00:00', 'break_end' => '18:30:00', 'is_night_shift' => false],
            ['name' => 'Shift C - Night', 'code' => 'C', 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'break_start' => '02:00:00', 'break_end' => '02:30:00', 'is_night_shift' => true],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(['code' => $shift['code']], $shift);
        }
    }
}
