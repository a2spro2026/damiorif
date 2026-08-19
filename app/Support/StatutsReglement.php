<?php

namespace App\Support;

class StatutsReglement
{
    public static function options(): array
    {
        return [
            'en_instance' => 'En Instance',
            'en_cours' => 'En Cour',
            'paye' => 'Payé',
            'imp' => 'Imp',
            'reporte' => 'Reporté',
            'devalide' => 'Dévalidé',
        ];
    }

    public static function colors(): array
    {
        return [
            'en_instance' => '#9ca3af',
            'en_cours' => '#3b82f6',
            'paye' => '#22c55e',
            'imp' => '#ef4444',
            'reporte' => '#eab308',
            'devalide' => '#a855f7',
        ];
    }

    public static function label(?string $key): string
    {
        return self::options()[$key] ?? (string) $key;
    }

    public static function color(?string $key): string
    {
        return self::colors()[$key] ?? '#9ca3af';
    }

    public static function default(): string
    {
        return 'en_instance';
    }
}
