<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date_reglement',
    'numero',
    'fournisseur_id',
    'nom_fournisseur',
    'type_reglement',
    'banque',
    'nom_tire',
    'montant',
    'date_decaissement',
    'statut',
])]
class ReglementAchat extends Model
{
    protected $table = 'reglements_achat';

    protected function casts(): array
    {
        return [
            'date_reglement' => 'date',
            'date_decaissement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(ReglementAchatLigne::class);
    }

    public static function nextNumero(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'RA-'.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
