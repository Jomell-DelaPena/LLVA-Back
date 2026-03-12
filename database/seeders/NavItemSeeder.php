<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NavItem;
use Illuminate\Database\Seeder;

class NavItemSeeder extends Seeder
{
    public function run(): void
    {
        // ── Ensure core modules exist ─────────────────────────────────────────
        $timeTracker = Module::firstOrCreate(
            ['code' => 'time_tracker'],
            ['name' => 'Time Tracker', 'description' => 'Employee time tracking, breaks, and idle monitoring.', 'active' => true]
        );

        $usersModule = Module::firstOrCreate(
            ['code' => 'users'],
            ['name' => 'Users', 'description' => 'System user account management.', 'active' => true]
        );

        $rolesModule = Module::firstOrCreate(
            ['code' => 'roles'],
            ['name' => 'Roles', 'description' => 'User role and access management.', 'active' => true]
        );

        // ── Top-level standalone items ────────────────────────────────────────
        NavItem::firstOrCreate(
            ['route' => '/dashboard'],
            [
                'title'      => 'Dashboard',
                'icon'       => 'mdi-view-dashboard-outline',
                'type'       => 'item',
                'sort_order' => 1,
                'active'     => true,
            ]
        );

        // ── Master Data directory ─────────────────────────────────────────────
        $masterData = NavItem::firstOrCreate(
            ['title' => 'Master Data', 'type' => 'directory', 'parent_id' => null],
            [
                'icon'       => 'mdi-database-outline',
                'sort_order' => 2,
                'active'     => true,
            ]
        );

        NavItem::firstOrCreate(
            ['route' => '/users'],
            [
                'parent_id'  => $masterData->id,
                'module_id'  => $usersModule->id,
                'title'      => 'Users',
                'icon'       => 'mdi-account-group-outline',
                'type'       => 'item',
                'sort_order' => 1,
                'active'     => true,
            ]
        );

        NavItem::firstOrCreate(
            ['route' => '/roles'],
            [
                'parent_id'  => $masterData->id,
                'module_id'  => $rolesModule->id,
                'title'      => 'Roles',
                'icon'       => 'mdi-shield-account-outline',
                'type'       => 'item',
                'sort_order' => 2,
                'active'     => true,
            ]
        );

        // ── Standalone module items ───────────────────────────────────────────
        NavItem::firstOrCreate(
            ['route' => '/time-tracker'],
            [
                'module_id'  => $timeTracker->id,
                'title'      => 'Time Tracker',
                'icon'       => 'mdi-clock-outline',
                'type'       => 'item',
                'sort_order' => 3,
                'active'     => true,
            ]
        );

        NavItem::firstOrCreate(
            ['route' => '/reports'],
            [
                'title'      => 'Reports',
                'icon'       => 'mdi-chart-bar',
                'type'       => 'item',
                'sort_order' => 4,
                'active'     => true,
            ]
        );

        // ── Settings directory ────────────────────────────────────────────────
        $settings = NavItem::firstOrCreate(
            ['title' => 'Settings', 'type' => 'directory', 'parent_id' => null],
            [
                'icon'       => 'mdi-cog-outline',
                'sort_order' => 5,
                'active'     => true,
            ]
        );

        NavItem::firstOrCreate(
            ['route' => '/settings/accesses'],
            [
                'parent_id'  => $settings->id,
                'title'      => 'Accesses',
                'icon'       => 'mdi-shield-key-outline',
                'type'       => 'item',
                'sort_order' => 1,
                'active'     => true,
            ]
        );
    }
}
