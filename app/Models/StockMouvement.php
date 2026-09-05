<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date_mouvement',
    'numero',
    'type',
    'depot',
    'depot_destination',
    'note',
    'user_id',
    'user_name',
])]
class StockMouvement extends Model
{
    protected function casts(): array
    {
        return [
            'date_mouvement' => 'date',
        ];
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(StockMouvementLigne::class);
    }

    public static function nextNumero(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero');
        $n = 1;
        if (is_string($last) && preg_match('/MS-(\d+)/', $last, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return 'MS-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public static function nextRetourNumero(): string
    {
        $last = static::query()
            ->where('numero', 'like', 'RT-%')
            ->orderByDesc('id')
            ->value('numero');
        $n = 1;
        if (is_string($last) && preg_match('/RT-(\d+)/', $last, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return 'RT-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public static function nextAjustementNumero(): string
    {
        $last = static::query()
            ->where('numero', 'like', 'AJ-%')
            ->orderByDesc('id')
            ->value('numero');
        $n = 1;
        if (is_string($last) && preg_match('/AJ-(\d+)/', $last, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return 'AJ-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }
}
