<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Abdelilah',
            'username' => 'abdelilah',
            'email' => 'abdelilah@damiorif.ma',
            'password' => 'password',
            'statut' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Administrateur',
            'username' => 'admin',
            'email' => 'admin@damiorif.ma',
            'password' => 'admin123',
            'statut' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'manager@damiorif.ma',
            'password' => 'manager123',
            'statut' => 'manager',
        ]);

        User::factory()->create([
            'name' => 'Employé',
            'username' => 'employe',
            'email' => 'employe@damiorif.ma',
            'password' => 'employe123',
            'statut' => 'employe',
        ]);
    }
}
