<?php

namespace App\Support;

trait SyncsNomParametre
{
    public static function syncFrom(?string $nom): void
    {
        $nom = trim((string) $nom);

        if ($nom === '') {
            return;
        }

        $exists = static::query()
            ->whereRaw('LOWER(nom) = ?', [mb_strtolower($nom)])
            ->exists();

        if (! $exists) {
            static::query()->create(['nom' => $nom]);
        }
    }
}
