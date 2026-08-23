<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AppMenus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'abdelilah'],
            [
                'name' => 'Abdelilah',
                'email' => 'abdelilah@damiorif.ma',
                'password' => Hash::make('password'),
                'mot_de_passe' => 'password',
                'statut' => 'directeur',
                'autorisations' => AppMenus::allPermissionKeys(),
            ]
        );

        if (app()->environment('local')) {
            User::query()->updateOrCreate(
                ['username' => 'yahya'],
                [
                    'name' => 'Yahya',
                    'contact' => '0661755048',
                    'email' => 'yahya@damiorif.local',
                    'password' => Hash::make('password'),
                    'mot_de_passe' => 'password',
                    'statut' => 'directeur',
                    'autorisations' => AppMenus::allPermissionKeys(),
                ]
            );
        }
    }
}
