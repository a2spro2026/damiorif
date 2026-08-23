<?php

namespace App\Support;

use App\Models\User;

class UserAccess
{
    /**
     * Menus autorisés pour un utilisateur de dépôt.
     *
     * @return list<string>
     */
    public static function depotMenuKeys(): array
    {
        return [
            'clients.fiche',
            'clients.bon_vente',
            'clients.reglement_vente',
            'clients.balance',
            'clients.releve_compte',
            'stock.depot',
            'stock.mouvement',
            'stock.commande_depot',
            'charges.etat_charges',
            'charges.etat_depenses',
        ];
    }

    public static function isDepotUser(?User $user): bool
    {
        if (! $user || ! is_string($user->statut)) {
            return false;
        }

        return str_starts_with($user->statut, 'depot_');
    }

    public static function depotKey(?User $user): ?string
    {
        if (! self::isDepotUser($user)) {
            return null;
        }

        $key = substr((string) $user->statut, strlen('depot_'));

        return array_key_exists($key, Depots::options()) ? $key : null;
    }

    public static function depotLabel(?User $user): ?string
    {
        $key = self::depotKey($user);

        return $key ? (Depots::options()[$key] ?? $key) : null;
    }

    /**
     * null = accès complet (admin / rôles non dépôt).
     *
     * @return list<string>|null
     */
    public static function allowedMenuKeys(?User $user): ?array
    {
        if (! $user) {
            return [];
        }

        if (self::isDepotUser($user)) {
            return self::depotMenuKeys();
        }

        return null;
    }

    public static function canAccessRoute(?User $user, string $routeName): bool
    {
        if (! $user) {
            return false;
        }

        if (in_array($routeName, ['dashboard', 'logout', 'login'], true)) {
            return true;
        }

        $allowed = self::allowedMenuKeys($user);
        if ($allowed === null) {
            return true;
        }

        foreach ($allowed as $key) {
            if ($routeName === $key || str_starts_with($routeName, $key.'.')) {
                return true;
            }
        }

        // CRUD charges partagé entre états
        if (preg_match('/^charges\.(update|destroy|store)$/', $routeName)
            && (in_array('charges.etat_charges', $allowed, true) || in_array('charges.etat_depenses', $allowed, true))) {
            return true;
        }

        // Accueil module (clients.index / charges.index)
        if (preg_match('/^([a-z]+)\.index$/', $routeName, $m)) {
            $module = $m[1];
            foreach ($allowed as $key) {
                if ($key === $module || str_starts_with($key, $module.'.')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<string, array{label: string, icon: string, children: array<int, array<string, mixed>>}>
     */
    public static function navigationFor(?User $user): array
    {
        $nav = AppMenus::navigation();
        $allowed = self::allowedMenuKeys($user);

        if ($allowed === null) {
            return $nav;
        }

        $filtered = [];

        foreach ($nav as $moduleKey => $section) {
            $children = array_values(array_filter(
                $section['children'],
                fn (array $child) => in_array($child['key'], $allowed, true)
            ));

            if ($children === []) {
                continue;
            }

            $section['children'] = $children;
            $filtered[$moduleKey] = $section;
        }

        return $filtered;
    }

    /**
     * @return array<string, string>
     */
    public static function depotOptionsFor(?User $user): array
    {
        $all = Depots::options();
        $key = self::depotKey($user);

        if ($key === null) {
            return $all;
        }

        return [$key => $all[$key]];
    }
}
