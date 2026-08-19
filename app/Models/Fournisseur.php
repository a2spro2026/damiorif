<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'date_fiche',
    'ref_frns',
    'nom_fournisseur',
    'nom_gerant',
    'contact',
    'ville',
    'type_reglement',
    'banque',
    'rib',
])]
class Fournisseur extends Model
{
    protected function casts(): array
    {
        return [
            'date_fiche' => 'date',
        ];
    }

    public static function nextRef(): string
    {
        $last = static::query()->orderByDesc('id')->value('ref_frns');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'FRN-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function bonsAchat()
    {
        return $this->hasMany(BonAchat::class);
    }
}
