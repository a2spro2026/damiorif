<?php

namespace App\Support;

class AppMenus
{
    public static function statutOptions(): array
    {
        return [
            'directeur' => 'Directeur',
            'gerant' => 'Gérant',
            'facturation' => 'Facturation',
            'magasinier' => 'Magasinier',
            'depot_tanger' => 'Depot Tanger',
            'depot_nador' => 'Depot Nador',
            'depot_tetouan' => 'Depot Tetouan',
            'depot_houcima' => 'Depot Houcima',
            'depot_belkciri' => 'Depot Belkciri',
            'depot_damiorif' => 'Dépôt DamioRif',
        ];
    }

    public static function statutLabel(?string $statut): string
    {
        return self::statutOptions()[$statut] ?? (string) $statut;
    }

    /**
     * Navigation principale (menus + sous-menus).
     *
     * @return array<string, array{
     *   label: string,
     *   icon: string,
     *   children: array<int, array{key: string, label: string, route: string, icon: string, group?: string}>
     * }>
     */
    public static function navigation(): array
    {
        return [
            'fournisseurs' => [
                'label' => 'Fournisseur',
                'icon' => 'fournisseurs',
                'children' => [
                    ['key' => 'fournisseurs.fiche', 'label' => 'Fiche Fournisseur', 'route' => 'fournisseurs.fiche', 'icon' => 'file'],
                    ['key' => 'fournisseurs.bon_achat', 'label' => "Bon D'achat", 'route' => 'fournisseurs.bon_achat', 'icon' => 'cart'],
                    ['key' => 'fournisseurs.reglement_achat', 'label' => 'Réglement Achat', 'route' => 'fournisseurs.reglement_achat', 'icon' => 'payment'],
                    ['key' => 'fournisseurs.balance', 'label' => 'Balance Fournisseurs', 'route' => 'fournisseurs.balance', 'icon' => 'balance'],
                ],
            ],
            'clients' => [
                'label' => 'Client',
                'icon' => 'clients',
                'children' => [
                    ['key' => 'clients.fiche', 'label' => 'Fiche Client', 'route' => 'clients.fiche', 'icon' => 'file'],
                    ['key' => 'clients.bon_vente', 'label' => 'Bon Vente', 'route' => 'clients.bon_vente', 'icon' => 'receipt'],
                    ['key' => 'clients.reglement_vente', 'label' => 'Réglement Vente', 'route' => 'clients.reglement_vente', 'icon' => 'payment'],
                    ['key' => 'clients.balance', 'label' => 'Balance Clients', 'route' => 'clients.balance', 'icon' => 'balance'],
                ],
            ],
            'stock' => [
                'label' => 'Stock',
                'icon' => 'stock',
                'children' => [
                    ['key' => 'stock.fiche_produit', 'label' => 'Fiche Produit', 'route' => 'stock.fiche_produit', 'icon' => 'package'],
                    ['key' => 'stock.depot_tanger', 'label' => 'Depot Tanger', 'route' => 'stock.depot_tanger', 'icon' => 'warehouse'],
                    ['key' => 'stock.depot_nador', 'label' => 'Depot Nador', 'route' => 'stock.depot_nador', 'icon' => 'warehouse'],
                    ['key' => 'stock.depot_tetouan', 'label' => 'Depot Tetouan', 'route' => 'stock.depot_tetouan', 'icon' => 'warehouse'],
                    ['key' => 'stock.depot_houcima', 'label' => 'Depot Houcima', 'route' => 'stock.depot_houcima', 'icon' => 'warehouse'],
                    ['key' => 'stock.depot_belkciri', 'label' => 'Depot Belkciri', 'route' => 'stock.depot_belkciri', 'icon' => 'warehouse'],
                    ['key' => 'stock.depot_damiorif', 'label' => 'Dépôt DamioRif', 'route' => 'stock.depot_damiorif', 'icon' => 'warehouse'],
                    ['key' => 'stock.mouvement', 'label' => 'Mouvement Stock', 'route' => 'stock.mouvement', 'icon' => 'transfer'],
                ],
            ],
            'charges' => [
                'label' => 'Charges',
                'icon' => 'charges',
                'children' => [
                    ['key' => 'charges.etat_charges', 'label' => 'Etat Charges', 'route' => 'charges.etat_charges', 'icon' => 'list'],
                    ['key' => 'charges.etat_depenses', 'label' => 'Etat Depenses', 'route' => 'charges.etat_depenses', 'icon' => 'wallet'],
                ],
            ],
            'rapports' => [
                'label' => 'Rapports',
                'icon' => 'rapports',
                'children' => [
                    ['key' => 'rapports.releve_fournisseurs', 'label' => 'Relevés Compte Fournisseurs', 'route' => 'rapports.releve_fournisseurs', 'icon' => 'report'],
                    ['key' => 'rapports.releve_clients', 'label' => 'Relevé Compte Clients', 'route' => 'rapports.releve_clients', 'icon' => 'report'],
                    ['key' => 'rapports.releve_caisse', 'label' => 'Relevés Compte Caisse', 'route' => 'rapports.releve_caisse', 'icon' => 'cash'],
                    ['key' => 'rapports.releve_tresorerie', 'label' => 'Relevé Compte Trésorerie', 'route' => 'rapports.releve_tresorerie', 'icon' => 'bank'],
                    ['key' => 'rapports.releve_depots', 'label' => 'Relevé Compte Depots', 'route' => 'rapports.releve_depots', 'icon' => 'warehouse'],
                    ['key' => 'rapports.releve_charges', 'label' => 'Relevés Compte Charges et Dépenses', 'route' => 'rapports.releve_charges', 'icon' => 'report'],
                ],
            ],
            'configuration' => [
                'label' => 'Configuration',
                'icon' => 'configuration',
                'children' => [
                    ['key' => 'configuration.utilisateurs', 'label' => 'Utilisateurs', 'route' => 'configuration.utilisateurs.index', 'icon' => 'users'],
                    ['key' => 'configuration.parametres.ville', 'label' => 'Ville', 'route' => 'configuration.parametres.ville', 'icon' => 'map', 'group' => 'Paramètres'],
                    ['key' => 'configuration.parametres.banque', 'label' => 'Banque', 'route' => 'configuration.parametres.banque', 'icon' => 'bank', 'group' => 'Paramètres'],
                    ['key' => 'configuration.parametres.tresoreries', 'label' => 'Trésoreries', 'route' => 'configuration.parametres.tresoreries', 'icon' => 'safe', 'group' => 'Paramètres'],
                    ['key' => 'configuration.parametres.chauffeurs', 'label' => 'Chauffeurs', 'route' => 'configuration.parametres.chauffeurs', 'icon' => 'truck', 'group' => 'Paramètres'],
                    ['key' => 'configuration.parametres.reglements', 'label' => 'Réglements', 'route' => 'configuration.parametres.reglements', 'icon' => 'payment', 'group' => 'Paramètres'],
                    ['key' => 'configuration.parametres.unites', 'label' => 'Unités de mesure', 'route' => 'configuration.parametres.unites', 'icon' => 'ruler', 'group' => 'Paramètres'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, children?: array<string, string>}>
     */
    public static function autorisations(): array
    {
        $items = [];

        foreach (self::navigation() as $key => $section) {
            $children = [];
            foreach ($section['children'] as $child) {
                $label = isset($child['group'])
                    ? $child['group'].' › '.$child['label']
                    : $child['label'];
                $children[$child['key']] = $label;
            }

            $items[$key] = [
                'label' => $section['label'],
                'children' => $children,
            ];
        }

        return $items;
    }

    public static function autorisationLabels(array $keys): array
    {
        $labels = [];
        foreach (self::autorisations() as $key => $section) {
            if (in_array($key, $keys, true)) {
                $labels[] = $section['label'];
            }
            foreach ($section['children'] ?? [] as $childKey => $childLabel) {
                if (in_array($childKey, $keys, true)) {
                    $labels[] = $section['label'].' › '.$childLabel;
                }
            }
        }

        return $labels;
    }

    public static function allPermissionKeys(): array
    {
        $keys = [];
        foreach (self::autorisations() as $key => $section) {
            $keys[] = $key;
            foreach (array_keys($section['children'] ?? []) as $childKey) {
                $keys[] = $childKey;
            }
        }

        return $keys;
    }

    public static function pageTitle(string $routeName): string
    {
        foreach (self::navigation() as $section) {
            foreach ($section['children'] as $child) {
                if ($child['route'] === $routeName) {
                    return $child['label'];
                }
            }
        }

        return 'Page';
    }
}
