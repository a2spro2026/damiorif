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
                'password' => Hash::make('0661755048'),
                'mot_de_passe' => '0661755048',
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
                    'password' => Hash::make('0661755048'),
                    'mot_de_passe' => '0661755048',
                    'statut' => 'directeur',
                    'autorisations' => AppMenus::allPermissionKeys(),
                ]
            );

            User::query()->updateOrCreate(
                ['username' => 'zerragui'],
                [
                    'name' => 'Zerragui',
                    'contact' => '0661755048',
                    'email' => 'zerragui@damiorif.local',
                    'password' => Hash::make('0661755048'),
                    'mot_de_passe' => '0661755048',
                    'statut' => 'directeur',
                    'autorisations' => AppMenus::allPermissionKeys(),
                ]
            );
        }
    }
}
