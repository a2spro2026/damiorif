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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AjusterStockController extends Controller
{
    public function index(Request $request): View
    {
        $this->assertCanAdjust();

        $user = $request->user();
        $depots = $this->adjustableDepots($user);
        $preselect = (string) $request->query('depot', Depots::centralKey());
        if (! array_key_exists($preselect, $depots)) {
            $preselect = array_key_first($depots) ?: Depots::centralKey();
        }

        $ajustements = StockMouvement::query()
            ->with('lignes')
            ->where('type', 'ajustement')
            ->where('numero', 'like', 'AJ-%')
            ->when(
                UserAccess::depotKey($user) === Depots::centralKey(),
                fn ($q) => $q->whereIn('depot', array_keys($depots))
            )
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('stock.ajuster.index', [
            'depots' => $depots,
            'preselectDepot' => $preselect,
            'nextNumero' => StockMouvement::nextAjustementNumero(),
            'references' => ProduitReferenceService::catalogue(),
            'ajustements' => $ajustements,
            'depotLabels' => Depots::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertCanAdjust();

        $user = $request->user();
        $depots = $this->adjustableDepots($user);

        $data = $request->validate([
            'date_mouvement' => ['required', 'date'],
            'depot' => ['required', 'string', Rule::in(array_keys($depots))],
            'remarque' => ['required', 'string', 'max:1000'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.ref' => ['nullable', 'string', 'max:100'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.qte' => ['required', 'numeric', 'not_in:0'],
            'lignes.*.prix_unitaire' => ['nullable', 'numeric', 'min:0'],
        ], [
            'depot.required' => 'Sélectionnez un dépôt.',
            'remarque.required' => 'Indiquez une remarque pour justifier l\'ajustement.',
            'lignes.required' => 'Ajoutez au moins un article.',
            'lignes.*.qte.not_in' => 'La quantité d\'ajustement ne peut pas être 0.',
            'lignes.*.designation.required' => 'La désignation est obligatoire.',
        ]);

        $depotLabel = $depots[$data['depot']] ?? $data['depot'];
        $remarque = trim($data['remarque']);

        DB::transaction(function () use ($data, $user, $depotLabel, $remarque) {
            $mvt = StockMouvement::create([
                'date_mouvement' => $data['date_mouvement'],
                'numero' => StockMouvement::nextAjustementNumero(),
                'type' => 'ajustement',
                'depot' => $data['depot'],
                'depot_destination' => null,
                'note' => 'Ajustement '.$depotLabel.' — '.$remarque,
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
            ->route('stock.ajuster', ['depot' => $data['depot']])
            ->with('success', 'Ajustement enregistré pour '.$depotLabel.'.');
    }

    private function assertCanAdjust(): void
    {
        $user = auth()->user();
        if (UserAccess::isDepotUser($user) && UserAccess::depotKey($user) !== Depots::centralKey()) {
            abort(403, 'Seul le dépôt principal peut ajuster le stock.');
        }
    }

    /**
     * @return array<string, string>
     */
    private function adjustableDepots($user): array
    {
        if (! UserAccess::isDepotUser($user)) {
            return Depots::options();
        }

        return UserAccess::depotOptionsFor($user);
    }
}
