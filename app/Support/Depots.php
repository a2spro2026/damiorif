<?php

namespace App\Support;

class Depots
{
    public static function options(): array
    {
        return [
            'tanger' => 'Depot Tanger',
            'nador' => 'Depot Nador',
            'tetouan' => 'Depot Tetouan',
            'houcima' => 'Depot Houcima',
            'belkciri' => 'Depot Belkciri',
            'retour' => 'Dépôt Retour',
            'damiorif' => 'Dépôt DamioRif',
        ];
    }

    /**
     * Dépôts opérationnels (hors Dépôt Retour).
     *
     * @return array<string, string>
     */
    public static function operationalOptions(): array
    {
        return collect(self::options())
            ->except(self::retourKey())
            ->all();
    }

    public static function centralKey(): string
    {
        return 'damiorif';
    }

    public static function retourKey(): string
    {
        return 'retour';
    }

    /**
     * @return list<string>
     */
    public static function regionalKeys(): array
    {
        return ['tanger', 'nador', 'tetouan', 'houcima', 'belkciri'];
    }

    public static function isCentral(string $depotKey): bool
    {
        return $depotKey === self::centralKey();
    }

    public static function isRetour(string $depotKey): bool
    {
        return $depotKey === self::retourKey();
    }

    public static function isRegional(string $depotKey): bool
    {
        return in_array($depotKey, self::regionalKeys(), true);
    }
}
