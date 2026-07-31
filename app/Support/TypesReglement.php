<?php

namespace App\Support;

class TypesReglement
{
    public static function options(): array
    {
        return [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'traite' => 'Traite',
            'effet' => 'Effet',
        ];
    }

    public static function label(?string $key): ?string
    {
        if ($key === null || $key === '') {
            return null;
        }

        return self::options()[$key] ?? $key;
    }

    public static function keyFromLabel(string $label): ?string
    {
        $found = array_search($label, self::options(), true);

        return $found === false ? null : $found;
    }
}
