<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['date_fiche', 'ref_produit', 'nom_produit', 'unite'])]
class Produit extends Model
{
    protected function casts(): array
    {
        return [
            'date_fiche' => 'date',
        ];
    }

    public static function nextRef(): string
    {
        $last = static::query()->orderByDesc('id')->value('ref_produit');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'PRD-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }
}
