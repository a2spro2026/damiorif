<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceClientController;
use App\Http\Controllers\BalanceFournisseurController;
use App\Http\Controllers\BanqueController;
use App\Http\Controllers\BonAchatController;
use App\Http\Controllers\BonVenteController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepotStockController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ReglementAchatController;
use App\Http\Controllers\ReglementController;
use App\Http\Controllers\ReglementVenteController;
use App\Http\Controllers\ReleveCompteClientController;
use App\Http\Controllers\ReleveCompteFournisseurController;
use App\Http\Controllers\StockMouvementController;
use App\Http\Controllers\TresorerieController;
use App\Http\Controllers\UniteMesureController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\VilleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth', 'access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/fournisseurs', fn () => app(PageController::class)->moduleHome('fournisseurs'))->name('fournisseurs.index');
    Route::get('/stock', fn () => app(PageController::class)->moduleHome('stock'))->name('stock.index');
    Route::get('/clients', fn () => app(PageController::class)->moduleHome('clients'))->name('clients.index');
    Route::get('/charges', fn () => app(PageController::class)->moduleHome('charges'))->name('charges.index');
    Route::get('/rapports', fn () => app(PageController::class)->moduleHome('rapports'))->name('rapports.index');
    Route::get('/configuration', [PageController::class, 'configuration'])->name('configuration.index');

    // Fournisseurs
    Route::get('/fournisseurs/fiche', [FournisseurController::class, 'index'])->name('fournisseurs.fiche');
    Route::post('/fournisseurs/fiche', [FournisseurController::class, 'store'])->name('fournisseurs.fiche.store');
    Route::put('/fournisseurs/fiche/{fournisseur}', [FournisseurController::class, 'update'])->name('fournisseurs.fiche.update');
    Route::delete('/fournisseurs/fiche/{fournisseur}', [FournisseurController::class, 'destroy'])->name('fournisseurs.fiche.destroy');
    Route::get('/fournisseurs/bon-achat', [BonAchatController::class, 'index'])->name('fournisseurs.bon_achat');
    Route::post('/fournisseurs/bon-achat', [BonAchatController::class, 'store'])->name('fournisseurs.bon_achat.store');
    Route::put('/fournisseurs/bon-achat/{bonAchat}', [BonAchatController::class, 'update'])->name('fournisseurs.bon_achat.update');
    Route::delete('/fournisseurs/bon-achat/{bonAchat}', [BonAchatController::class, 'destroy'])->name('fournisseurs.bon_achat.destroy');
    Route::get('/fournisseurs/bon-achat/{bonAchat}/print', [BonAchatController::class, 'print'])->name('fournisseurs.bon_achat.print');
    Route::get('/fournisseurs/reglement-achat', [ReglementAchatController::class, 'index'])->name('fournisseurs.reglement_achat');
    Route::post('/fournisseurs/reglement-achat', [ReglementAchatController::class, 'store'])->name('fournisseurs.reglement_achat.store');
    Route::put('/fournisseurs/reglement-achat/{reglementAchat}', [ReglementAchatController::class, 'update'])->name('fournisseurs.reglement_achat.update');
    Route::patch('/fournisseurs/reglement-achat/{reglementAchat}/statut', [ReglementAchatController::class, 'updateStatut'])->name('fournisseurs.reglement_achat.statut');
    Route::delete('/fournisseurs/reglement-achat/{reglementAchat}', [ReglementAchatController::class, 'destroy'])->name('fournisseurs.reglement_achat.destroy');
    Route::get('/fournisseurs/reglement-achat/{reglementAchat}/print', [ReglementAchatController::class, 'print'])->name('fournisseurs.reglement_achat.print');
    Route::get('/fournisseurs/balance', [BalanceFournisseurController::class, 'index'])->name('fournisseurs.balance');
    Route::get('/fournisseurs/releve-compte', [ReleveCompteFournisseurController::class, 'index'])->name('fournisseurs.releve_compte');
    Route::get('/fournisseurs/releve-compte/print', [ReleveCompteFournisseurController::class, 'print'])->name('fournisseurs.releve_compte.print');

    // Stock
    Route::get('/stock/fiche-produit', fn () => redirect()->route('stock.depot_tanger'))->name('stock.fiche_produit');
    Route::get('/stock/depot-tanger', fn () => app(DepotStockController::class)->show('tanger'))->name('stock.depot_tanger');
    Route::get('/stock/depot-nador', fn () => app(DepotStockController::class)->show('nador'))->name('stock.depot_nador');
    Route::get('/stock/depot-tetouan', fn () => app(DepotStockController::class)->show('tetouan'))->name('stock.depot_tetouan');
    Route::get('/stock/depot-houcima', fn () => app(DepotStockController::class)->show('houcima'))->name('stock.depot_houcima');
    Route::get('/stock/depot-belkciri', fn () => app(DepotStockController::class)->show('belkciri'))->name('stock.depot_belkciri');
    Route::get('/stock/depot-damiorif', fn () => app(DepotStockController::class)->show('damiorif'))->name('stock.depot_damiorif');
    Route::get('/stock/mouvement', [StockMouvementController::class, 'index'])->name('stock.mouvement');
    Route::post('/stock/mouvement', [StockMouvementController::class, 'store'])->name('stock.mouvement.store');
    Route::delete('/stock/mouvement/{mouvement}', [StockMouvementController::class, 'destroy'])->name('stock.mouvement.destroy');

    // Clients
    Route::get('/clients/fiche', [ClientController::class, 'index'])->name('clients.fiche');
    Route::post('/clients/fiche', [ClientController::class, 'store'])->name('clients.fiche.store');
    Route::put('/clients/fiche/{client}', [ClientController::class, 'update'])->name('clients.fiche.update');
    Route::delete('/clients/fiche/{client}', [ClientController::class, 'destroy'])->name('clients.fiche.destroy');
    Route::get('/clients/bon-vente', [BonVenteController::class, 'index'])->name('clients.bon_vente');
    Route::post('/clients/bon-vente', [BonVenteController::class, 'store'])->name('clients.bon_vente.store');
    Route::put('/clients/bon-vente/{bonVente}', [BonVenteController::class, 'update'])->name('clients.bon_vente.update');
    Route::delete('/clients/bon-vente/{bonVente}', [BonVenteController::class, 'destroy'])->name('clients.bon_vente.destroy');
    Route::get('/clients/bon-vente/{bonVente}/print', [BonVenteController::class, 'print'])->name('clients.bon_vente.print');
    Route::get('/clients/reglement-vente', [ReglementVenteController::class, 'index'])->name('clients.reglement_vente');
    Route::post('/clients/reglement-vente', [ReglementVenteController::class, 'store'])->name('clients.reglement_vente.store');
    Route::put('/clients/reglement-vente/{reglementVente}', [ReglementVenteController::class, 'update'])->name('clients.reglement_vente.update');
    Route::patch('/clients/reglement-vente/{reglementVente}/statut', [ReglementVenteController::class, 'updateStatut'])->name('clients.reglement_vente.statut');
    Route::delete('/clients/reglement-vente/{reglementVente}', [ReglementVenteController::class, 'destroy'])->name('clients.reglement_vente.destroy');
    Route::get('/clients/reglement-vente/{reglementVente}/print', [ReglementVenteController::class, 'print'])->name('clients.reglement_vente.print');
    Route::get('/clients/balance', [BalanceClientController::class, 'index'])->name('clients.balance');
    Route::get('/clients/releve-compte', [ReleveCompteClientController::class, 'index'])->name('clients.releve_compte');
    Route::get('/clients/releve-compte/print', [ReleveCompteClientController::class, 'print'])->name('clients.releve_compte.print');

    // Charges
    Route::get('/charges/etat-charges', [ChargeController::class, 'indexCharges'])->name('charges.etat_charges');
    Route::post('/charges/etat-charges', [ChargeController::class, 'store'])->name('charges.etat_charges.store');
    Route::get('/charges/etat-depenses', [ChargeController::class, 'indexDepenses'])->name('charges.etat_depenses');
    Route::post('/charges/etat-depenses', [ChargeController::class, 'store'])->name('charges.etat_depenses.store');
    Route::put('/charges/{charge}', [ChargeController::class, 'update'])->name('charges.update');
    Route::delete('/charges/{charge}', [ChargeController::class, 'destroy'])->name('charges.destroy');

    // Rapports
    Route::get('/rapports/releve-fournisseurs', [RapportController::class, 'releveFournisseurs'])->name('rapports.releve_fournisseurs');
    Route::get('/rapports/releve-clients', [RapportController::class, 'releveClients'])->name('rapports.releve_clients');
    Route::get('/rapports/releve-caisse', [RapportController::class, 'releveCaisse'])->name('rapports.releve_caisse');
    Route::get('/rapports/releve-tresorerie', [RapportController::class, 'releveTresorerie'])->name('rapports.releve_tresorerie');
    Route::get('/rapports/releve-depots', [RapportController::class, 'releveDepots'])->name('rapports.releve_depots');
    Route::get('/rapports/releve-charges', [RapportController::class, 'releveCharges'])->name('rapports.releve_charges');

    // Configuration
    Route::prefix('configuration')->name('configuration.')->group(function () {
        Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::put('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

        Route::get('/parametres/ville', [VilleController::class, 'index'])->name('parametres.ville');

        Route::get('/parametres/banque', [BanqueController::class, 'index'])->name('parametres.banque');
        Route::post('/parametres/banque', [BanqueController::class, 'store'])->name('parametres.banque.store');
        Route::put('/parametres/banque/{banque}', [BanqueController::class, 'update'])->name('parametres.banque.update');
        Route::delete('/parametres/banque/{banque}', [BanqueController::class, 'destroy'])->name('parametres.banque.destroy');

        Route::get('/parametres/tresoreries', [TresorerieController::class, 'index'])->name('parametres.tresoreries');
        Route::post('/parametres/tresoreries', [TresorerieController::class, 'store'])->name('parametres.tresoreries.store');
        Route::put('/parametres/tresoreries/{tresorerie}', [TresorerieController::class, 'update'])->name('parametres.tresoreries.update');
        Route::delete('/parametres/tresoreries/{tresorerie}', [TresorerieController::class, 'destroy'])->name('parametres.tresoreries.destroy');

        Route::get('/parametres/chauffeurs', [ChauffeurController::class, 'index'])->name('parametres.chauffeurs');
        Route::post('/parametres/chauffeurs', [ChauffeurController::class, 'store'])->name('parametres.chauffeurs.store');
        Route::put('/parametres/chauffeurs/{chauffeur}', [ChauffeurController::class, 'update'])->name('parametres.chauffeurs.update');
        Route::delete('/parametres/chauffeurs/{chauffeur}', [ChauffeurController::class, 'destroy'])->name('parametres.chauffeurs.destroy');

        Route::get('/parametres/reglements', [ReglementController::class, 'index'])->name('parametres.reglements');
        Route::get('/parametres/unites', [UniteMesureController::class, 'index'])->name('parametres.unites');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
