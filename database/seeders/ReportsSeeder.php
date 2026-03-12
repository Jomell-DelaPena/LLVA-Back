<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NavItem;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Module ────────────────────────────────────────────────────────────
        $module = Module::firstOrCreate(
            ['code' => 'reports'],
            [
                'name'        => 'Reports',
                'description' => 'Workforce reporting and data export tools.',
                'active'      => true,
            ]
        );

        // ── Nav directory ─────────────────────────────────────────────────────
        $dir = NavItem::firstOrCreate(
            ['type' => 'directory', 'title' => 'Reports'],
            [
                'module_id'  => $module->id,
                'icon'       => 'mdi-chart-box-outline',
                'route'      => null,
                'sort_order' => 30,
                'active'     => true,
            ]
        );

        // ── Nav children ──────────────────────────────────────────────────────
        $children = [
            [
                'title'      => 'Time Tracker',
                'icon'       => 'mdi-clock-time-four-outline',
                'route'      => '/reports/time-tracker',
                'sort_order' => 1,
            ],
            [
                'title'      => 'Attendance',
                'icon'       => 'mdi-calendar-check-outline',
                'route'      => '/reports/attendance',
                'sort_order' => 2,
            ],
            [
                'title'      => 'Payroll',
                'icon'       => 'mdi-currency-usd',
                'route'      => '/reports/payroll',
                'sort_order' => 3,
            ],
        ];

        foreach ($children as $child) {
            NavItem::firstOrCreate(
                ['parent_id' => $dir->id, 'route' => $child['route']],
                array_merge($child, [
                    'parent_id'  => $dir->id,
                    'module_id'  => $module->id,
                    'type'       => 'item',
                    'active'     => true,
                ])
            );
        }

        $this->command->info('Reports module and nav items seeded.');
    }
}
