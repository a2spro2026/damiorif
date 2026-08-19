<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChargeController extends Controller
{
    public function indexCharges(): View
    {
        return $this->index('charge');
    }

    public function indexDepenses(): View
    {
        return $this->index('depense');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = auth()->user();

        Charge::create([
            'date_charge' => $data['date_charge'],
            'type' => $data['type'],
            'libelle' => $data['libelle'],
            'montant' => $data['montant'],
            'depot' => $data['depot'],
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        return redirect()->route($data['type'] === 'depense' ? 'charges.etat_depenses' : 'charges.etat_charges');
    }

    public function update(Request $request, Charge $charge): RedirectResponse
    {
        $this->assertAccess($charge);
        $data = $this->validated($request);

        $charge->update([
            'date_charge' => $data['date_charge'],
            'type' => $data['type'],
            'libelle' => $data['libelle'],
            'montant' => $data['montant'],
            'depot' => $data['depot'],
        ]);

        return redirect()->route($data['type'] === 'depense' ? 'charges.etat_depenses' : 'charges.etat_charges');
    }

    public function destroy(Charge $charge): RedirectResponse
    {
        $this->assertAccess($charge);
        $type = $charge->type;
        $charge->delete();

        return redirect()->route($type === 'depense' ? 'charges.etat_depenses' : 'charges.etat_charges');
    }

    private function index(string $type): View
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        $query = Charge::query()
            ->where('type', $type)
            ->orderByDesc('date_charge')
            ->orderByDesc('id');

        if ($depotKey) {
            $query->where('depot', $depotKey)->where('user_id', $user->id);
        }

        $title = $type === 'depense' ? 'Etat Depenses' : 'Etat Charges';

        return view('charges.index', [
            'items' => $query->get(),
            'type' => $type,
            'title' => $title,
            'depots' => UserAccess::depotOptionsFor($user),
            'lockedDepot' => $depotKey,
        ]);
    }

    private function validated(Request $request): array
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);
        $allowedDepots = array_keys(UserAccess::depotOptionsFor($user));

        $data = $request->validate([
            'date_charge' => ['required', 'date'],
            'type' => ['required', 'string', Rule::in(['charge', 'depense'])],
            'libelle' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'depot' => ['required', 'string', Rule::in($allowedDepots)],
        ]);

        if ($depotKey) {
            $data['depot'] = $depotKey;
        }

        return $data;
    }

    private function assertAccess(Charge $charge): void
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        if ($depotKey) {
            if ($charge->depot !== $depotKey || (int) $charge->user_id !== (int) $user->id) {
                abort(403, 'Cette ligne n\'appartient pas à votre dépôt.');
            }
        }
    }
}
