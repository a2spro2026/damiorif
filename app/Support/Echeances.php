<?php

namespace App\Support;

class Echeances
{
    public static function options(): array
    {
        return [
            'a_vue' => 'À vue',
            'esp' => 'Esp',
            '45' => '45 jrs',
            '60' => '60 jrs',
            '90' => '90 jrs',
            '120' => '120 jrs',
        ];
    }

    public static function label(?string $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return self::options()[(string) $value] ?? ((string) $value).' jrs';
    }
}
