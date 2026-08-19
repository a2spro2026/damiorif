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
            'versement' => 'Versement',
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

    public static function familyKey(?string $type): ?string
    {
        return match ($type) {
            'cheque' => 'cheque',
            'effet', 'traite' => 'effet',
            'especes' => 'especes',
            'virement' => 'virement',
            'versement' => 'versement',
            default => null,
        };
    }

    /**
     * @param  iterable<mixed>  $reglements
     * @return array{cheque: float, effet: float, especes: float, virement: float, versement: float}
     */
    public static function sumByFamily(iterable $reglements): array
    {
        $totals = [
            'cheque' => 0.0,
            'effet' => 0.0,
            'especes' => 0.0,
            'virement' => 0.0,
            'versement' => 0.0,
        ];

        foreach ($reglements as $reglement) {
            $key = self::familyKey($reglement->type_reglement ?? null);
            if ($key !== null) {
                $totals[$key] += (float) ($reglement->montant ?? 0);
            }
        }

        foreach ($totals as $key => $value) {
            $totals[$key] = round($value, 2);
        }

        return $totals;
    }
}
