<?php

namespace Database\Seeders;

use App\Models\Access;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleAccessSeeder extends Seeder
{
    public function run(): void
    {
        // ── Modules ────────────────────────────────────────────────────────────
        $timeTracker = Module::firstOrCreate(
            ['code' => 'time_tracker'],
            ['name' => 'Time Tracker', 'description' => 'Employee time tracking, breaks, and idle monitoring.', 'active' => true]
        );

        // ── Accesses ───────────────────────────────────────────────────────────
        Access::firstOrCreate(
            ['code' => 'notifications'],
            [
                'module_id'   => $timeTracker->id,
                'name'        => 'Notifications',
                'description' => 'Receive Time Tracker alerts and notifications.',
                'active'      => true,
            ]
        );
    }
}
