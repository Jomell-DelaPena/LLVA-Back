<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedNewUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([ 
            'name' => 'Admin', 
            'email' => 'admin@gmail.com', 
            'email_verified_at' => now(), 
            'password' => Hash::make('admin123'), 
            'remember_token' => Str::random(10), 
            'created_at' => now(), 
            'updated_at' => now(), 
            'deleted_at' => null, 
        ]);

        User::create([ 
            'name' => 'Jomell', 
            'email' => 'jomellhopedelapena@gmail.com', 
            'email_verified_at' => now(), 
            'password' => Hash::make('jomell123'), 
            'remember_token' => Str::random(10), 
            'created_at' => now(), 
            'updated_at' => now(), 
            'deleted_at' => null, 
        ]);
    }
}
