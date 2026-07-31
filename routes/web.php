<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceClientController;
use App\Http\Controllers\BonAchatController;
use App\Http\Controllers\BonVenteController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ReglementAchatController;
use App\Http\Controllers\ReglementController;
use App\Http\Controllers\ReglementVenteController;
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
    Route::get('/fournisseurs/balance', fn () => app(PageController::class)->show('Balance Fournisseurs'))->name('fournisseurs.balance');

    // Stock
    Route::get('/stock/fiche-produit', [ProduitController::class, 'index'])->name('stock.fiche_produit');
    Route::post('/stock/fiche-produit', [ProduitController::class, 'store'])->name('stock.fiche_produit.store');
    Route::put('/stock/fiche-produit/{produit}', [ProduitController::class, 'update'])->name('stock.fiche_produit.update');
    Route::delete('/stock/fiche-produit/{produit}', [ProduitController::class, 'destroy'])->name('stock.fiche_produit.destroy');
    Route::get('/stock/depot-tanger', fn () => app(PageController::class)->show('Depot Tanger'))->name('stock.depot_tanger');
    Route::get('/stock/depot-nador', fn () => app(PageController::class)->show('Depot Nador'))->name('stock.depot_nador');
    Route::get('/stock/depot-tetouan', fn () => app(PageController::class)->show('Depot Tetouan'))->name('stock.depot_tetouan');
    Route::get('/stock/depot-houcima', fn () => app(PageController::class)->show('Depot Houcima'))->name('stock.depot_houcima');
    Route::get('/stock/depot-belkciri', fn () => app(PageController::class)->show('Depot Belkciri'))->name('stock.depot_belkciri');
    Route::get('/stock/depot-damiorif', fn () => app(PageController::class)->show('Dépôt DamioRif'))->name('stock.depot_damiorif');
    Route::get('/stock/mouvement', fn () => app(PageController::class)->show('Mouvement Stock'))->name('stock.mouvement');

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

    // Charges
    Route::get('/charges/etat-charges', [ChargeController::class, 'indexCharges'])->name('charges.etat_charges');
    Route::post('/charges/etat-charges', [ChargeController::class, 'store'])->name('charges.etat_charges.store');
    Route::get('/charges/etat-depenses', [ChargeController::class, 'indexDepenses'])->name('charges.etat_depenses');
    Route::post('/charges/etat-depenses', [ChargeController::class, 'store'])->name('charges.etat_depenses.store');
    Route::put('/charges/{charge}', [ChargeController::class, 'update'])->name('charges.update');
    Route::delete('/charges/{charge}', [ChargeController::class, 'destroy'])->name('charges.destroy');

    // Rapports
    Route::get('/rapports/releve-fournisseurs', fn () => app(PageController::class)->show('Relevés Compte Fournisseurs'))->name('rapports.releve_fournisseurs');
    Route::get('/rapports/releve-clients', fn () => app(PageController::class)->show('Relevé Compte Clients'))->name('rapports.releve_clients');
    Route::get('/rapports/releve-caisse', fn () => app(PageController::class)->show('Relevés Compte Caisse'))->name('rapports.releve_caisse');
    Route::get('/rapports/releve-tresorerie', fn () => app(PageController::class)->show('Relevé Compte Trésorerie'))->name('rapports.releve_tresorerie');
    Route::get('/rapports/releve-depots', fn () => app(PageController::class)->show('Relevé Compte Depots'))->name('rapports.releve_depots');
    Route::get('/rapports/releve-charges', fn () => app(PageController::class)->show('Relevés Compte Charges et Dépenses'))->name('rapports.releve_charges');

    // Configuration
    Route::prefix('configuration')->name('configuration.')->group(function () {
        Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::put('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

        Route::get('/parametres/ville', [VilleController::class, 'index'])->name('parametres.ville');
        Route::get('/parametres/banque', fn () => app(PageController::class)->show('Banque'))->name('parametres.banque');
        Route::get('/parametres/tresoreries', fn () => app(PageController::class)->show('Trésoreries'))->name('parametres.tresoreries');
        Route::get('/parametres/chauffeurs', fn () => app(PageController::class)->show('Chauffeurs'))->name('parametres.chauffeurs');
        Route::get('/parametres/reglements', [ReglementController::class, 'index'])->name('parametres.reglements');
        Route::get('/parametres/unites', [UniteMesureController::class, 'index'])->name('parametres.unites');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
