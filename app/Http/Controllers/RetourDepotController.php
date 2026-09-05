<?php

namespace App\Http\Controllers;

use App\Models\StockMouvement;
use App\Support\Depots;
use App\Support\ProduitReferenceService;
use App\Support\StockDepotService;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RetourDepotController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        // Création : dépôts régionaux uniquement. Central : consultation.
        $canCreate = $depotKey !== null && Depots::isRegional($depotKey);

        $query = StockMouvement::query()
            ->with('lignes')
            ->where('type', 'transfert')
            ->where('depot_destination', Depots::retourKey())
            ->where('numero', 'like', 'RT-%')
            ->orderByDesc('id');

        if ($canCreate) {
            $query->where('depot', $depotKey);
        }

        $retours = $query->limit(80)->get();

        return view('stock.retour.index', [
            'canCreate' => $canCreate,
            'depotKey' => $depotKey,
            'depotLabel' => $depotKey ? (Depots::options()[$depotKey] ?? $depotKey) : null,
            'nextNumero' => StockMouvement::nextRetourNumero(),
            'references' => ProduitReferenceService::catalogue(),
            'stockDepot' => $canCreate ? StockDepotService::stockMapForDepot($depotKey) : [],
            'retours' => $retours,
            'depotLabels' => Depots::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $depotKey = UserAccess::depotKey($user);

        if (! $depotKey || ! Depots::isRegional($depotKey)) {
            abort(403, 'Seul un dépôt régional peut enregistrer un retour.');
        }

        $data = $request->validate([
            'date_mouvement' => ['required', 'date'],
            'remarque' => ['required', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.qte' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['nullable', 'numeric', 'min:0'],
        ], [
            'remarque.required' => 'Indiquez une remarque pour justifier le retour.',
            'lignes.required' => 'Ajoutez au moins un article.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
        ]);

        $stockMap = StockDepotService::stockMapForDepot($depotKey);
        $requested = [];
        foreach ($data['lignes'] as $ligne) {
            $key = StockDepotService::productKey($ligne['ref'] ?? null, $ligne['designation']);
            $requested[$key] = ($requested[$key] ?? 0.0) + (float) $ligne['qte'];
        }
        foreach ($requested as $key => $qty) {
            $available = $stockMap[$key] ?? 0.0;
            if ($available <= 0.0005) {
                throw ValidationException::withMessages([
                    'lignes' => 'Impossible de retourner un article en rupture de stock.',
                ]);
            }
            if ($qty > $available + 0.0005) {
                throw ValidationException::withMessages([
                    'lignes' => 'Quantité supérieure au stock disponible du dépôt.',
                ]);
            }
        }

        $remarque = trim($data['remarque']);
        $depotLabel = Depots::options()[$depotKey] ?? $depotKey;

        DB::transaction(function () use ($data, $depotKey, $user, $remarque, $depotLabel) {
            $mvt = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextRetourNumero(),
                'type' => 'transfert',
                'depot' => $depotKey,
                'depot_destination' => Depots::retourKey(),
                'note' => 'Retour '.$depotLabel.' — '.$remarque,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $mvt->lignes()->create([
                    'ref_produit' => $ligne['ref'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['qte'],
                ]);
            }

            ProduitReferenceService::syncFromBonAchatLignes($data['lignes']);
        });

        return redirect()
            ->route('stock.retour')
            ->with('success', 'Retour enregistré vers le Dépôt Retour.');
    }
}
