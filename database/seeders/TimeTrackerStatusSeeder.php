<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class TimeTrackerStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Time In',         'code' => 'TIME_IN',          'color' => '#43A047', 'active' => true],
            ['name' => 'Time Out',         'code' => 'TIME_OUT',         'color' => '#E53935', 'active' => true],
            ['name' => 'Break',            'code' => 'BREAK',            'color' => '#FB8C00', 'active' => true],
            ['name' => 'Back from Break',  'code' => 'BACK_FROM_BREAK',  'color' => '#039BE5', 'active' => true],
            ['name' => 'Break Overtime',   'code' => 'BREAK_OVERTIME',   'color' => '#D32F2F', 'active' => true],
        ];

        foreach ($statuses as $data) {
            Status::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
