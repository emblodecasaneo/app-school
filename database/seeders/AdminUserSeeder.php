<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un utilisateur admin existe déjà
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@lyncosc.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
            
            $this->command->info('Utilisateur administrateur créé avec succès!');
            $this->command->info('Email: admin@lyncosc.com');
            $this->command->info('Mot de passe: password');
        } else {
            $this->command->info('Un utilisateur administrateur existe déjà.');
        }
    }
} 