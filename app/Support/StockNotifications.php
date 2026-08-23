<?php

namespace App\Support;

use App\Models\BonCommandeDepot;
use App\Models\User;

class StockNotifications
{
    /**
     * Commandes régionales envoyées en attente d'expédition (vue centrale).
     */
    public static function pendingCommandes(?User $user = null): int
    {
        if (UserAccess::depotKey($user ?? auth()->user())) {
            return 0;
        }

        return BonCommandeDepot::query()
            ->where('statut', 'envoye')
            ->count();
    }
}
