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
    'montant',
    'user_id',
    'user_name',
])]
class StockMouvement extends Model
{
    protected function casts(): array
    {
        return [
            'date_mouvement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(StockMouvementLigne::class);
    }

    public static function nextNumero(): string
    {
        return static::nextNumeroForPrefix('MS');
    }

    public static function nextRetourNumero(): string
    {
        return static::nextNumeroForPrefix('RT');
    }

    public static function nextAjustementNumero(): string
    {
        return static::nextNumeroForPrefix('AJ');
    }

    /**
     * MS, RT and AJ share the same table and unique index, so the next number
     * must come from the highest number of the same prefix, not the last row.
     */
    private static function nextNumeroForPrefix(string $prefix): string
    {
        $max = 0;
        $pattern = '/^'.preg_quote($prefix, '/').'-(\d+)$/';

        static::query()
            ->where('numero', 'like', $prefix.'-%')
            ->pluck('numero')
            ->each(function ($numero) use ($pattern, &$max) {
                if (is_string($numero) && preg_match($pattern, $numero, $m)) {
                    $max = max($max, (int) $m[1]);
                }
            });

        return $prefix.'-'.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
