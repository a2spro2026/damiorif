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
            'damiorif' => 'Dépôt DamioRif',
        ];
    }

    public static function centralKey(): string
    {
        return 'damiorif';
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

    public static function isRegional(string $depotKey): bool
    {
        return in_array($depotKey, self::regionalKeys(), true);
    }
}
