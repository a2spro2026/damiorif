<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'date_fiche',
    'ref_client',
    'nom_client',
    'nom_gerant',
    'contact',
    'ville',
    'type_reglement',
    'banque',
    'rib',
    'depot',
    'user_id',
])]
class Client extends Model
{
    protected function casts(): array
    {
        return [
            'date_fiche' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, ?User $user): Builder
    {
        if (! $user || ! $user->isDepotUser()) {
            return $query;
        }

        $depot = $user->depotKey();

        return $query
            ->where('depot', $depot)
            ->where('user_id', $user->id);
    }

    public static function nextRef(): string
    {
        $last = static::query()->orderByDesc('id')->value('ref_client');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'CLI-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }
}
