<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AppMenus;
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
            'mot_de_passe' => 'password',
            'statut' => 'directeur',
            'autorisations' => AppMenus::allPermissionKeys(),
        ]);
    }
}
