<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'cin', 'contact', 'username', 'email', 'password', 'mot_de_passe', 'statut', 'autorisations'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'autorisations' => 'array',
        ];
    }

    public function displayId(): string
    {
        return 'ID'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public static function formatDisplayId(int|string|null $id): string
    {
        if ($id === null || $id === '') {
            return 'Auto';
        }

        return 'ID'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    public function isDepotUser(): bool
    {
        return \App\Support\UserAccess::isDepotUser($this);
    }

    public function depotKey(): ?string
    {
        return \App\Support\UserAccess::depotKey($this);
    }
}
