<?php

namespace App\Http\Controllers;

use App\Models\StockMouvement;
use App\Support\Depots;
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
        $depot = (string) $request->query('depot', Depots::centralKey());
        if (! array_key_exists($depot, $depots)) {
            $depot = array_key_first($depots) ?: Depots::centralKey();
        }

        $stockRows = StockDepotService::detailForDepot($depot)
            ->map(fn (array $row) => [
                'ref' => $row['ref'],
                'designation' => $row['designation'],
                'qte_en_stock' => (float) $row['qte_en_stock'],
            ])
            ->values()
            ->all();

        return view('stock.ajuster.index', [
            'depots' => $depots,
            'depot' => $depot,
            'depotLabel' => $depots[$depot] ?? $depot,
            'stockRows' => $stockRows,
            'nextNumero' => StockMouvement::nextAjustementNumero(),
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
        ], [
            'depot.required' => 'Sélectionnez un dépôt.',
            'remarque.required' => 'Indiquez une remarque pour justifier l\'ajustement.',
            'lignes.required' => 'Ajustez au moins une quantité (+/−).',
            'lignes.*.qte.not_in' => 'La quantité d\'ajustement ne peut pas être 0.',
        ]);

        $lignes = collect($data['lignes'])
            ->filter(fn ($l) => abs((float) ($l['qte'] ?? 0)) > 0.0005)
            ->values()
            ->all();

        if ($lignes === []) {
            return back()
                ->withErrors(['lignes' => 'Ajustez au moins une quantité (+/−).'])
                ->withInput();
        }

        $depotLabel = $depots[$data['depot']] ?? $data['depot'];
        $remarque = trim($data['remarque']);

        DB::transaction(function () use ($data, $user, $depotLabel, $remarque, $lignes) {
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

            foreach ($lignes as $ligne) {
                $mvt->lignes()->create([
                    'ref_produit' => ($ligne['ref'] ?? null) === '—' ? null : ($ligne['ref'] ?? null),
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['qte'],
                ]);
            }
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
