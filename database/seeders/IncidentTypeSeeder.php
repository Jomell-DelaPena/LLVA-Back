<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('incident_types')->insert([
            [
                'name' => 'Power Outage',
                'code' => 'PWR-001',
                'description' => 'Loss of electrical power preventing the employee from using their work equipment or internet connection.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Internet Connectivity Issue',
                'code' => 'NET-001',
                'description' => 'Unstable, slow, or unavailable internet connection preventing access to work systems.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Computer Hardware Failure',
                'code' => 'HW-001',
                'description' => 'A required hardware device such as a laptop, monitor, or keyboard is malfunctioning or unusable.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Software/Application Issue',
                'code' => 'SW-001',
                'description' => 'A required work application or software is not functioning properly or cannot be accessed.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'VPN Access Issue',
                'code' => 'VPN-001',
                'description' => 'The employee cannot connect to the company VPN required to access internal systems.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Account/Login Issue',
                'code' => 'ACC-001',
                'description' => 'The employee cannot access their account due to authentication errors, locked accounts, or password issues.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Security Incident',
                'code' => 'SEC-001',
                'description' => 'Suspicious or malicious activity such as phishing attempts, malware alerts, or unauthorized access.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Equipment Damage',
                'code' => 'EQP-001',
                'description' => 'Physical damage to work equipment preventing proper use.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Environmental Disruption',
                'code' => 'ENV-001',
                'description' => 'External conditions such as severe weather, flooding, or safety concerns preventing work.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Communication Tool Failure',
                'code' => 'COM-001',
                'description' => 'Communication platforms such as Slack, Teams, Zoom, or email are not functioning.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'ISP Service Outage',
                'code' => 'ISP-001',
                'description' => 'The employee’s internet service provider is experiencing an outage affecting their area.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'name' => 'Local Network Issue',
                'code' => 'NET-002',
                'description' => 'Issues with the employee’s home router, modem, or local network configuration.',
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ]);
    }
}