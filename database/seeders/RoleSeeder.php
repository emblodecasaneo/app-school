<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        User::create([
            'name' => 'Admin',
            'email' => 'admin@lyncosc.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Créer un enseignant
        User::create([
            'name' => 'Enseignant',
            'email' => 'teacher@lyncosc.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Créer un intendant
        User::create([
            'name' => 'Intendant',
            'email' => 'intendant@lyncosc.com',
            'password' => Hash::make('password'),
            'role' => 'intendant',
        ]);
    }
}
