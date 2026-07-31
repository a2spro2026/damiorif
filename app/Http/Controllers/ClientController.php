<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Reglement;
use App\Models\Ville;
use App\Support\TypesReglement;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('clients.fiche.index', [
            'clients' => Client::query()->forUser($user)->orderByDesc('id')->get(),
            'typesReglement' => TypesReglement::options(),
            'nextRef' => Client::nextRef(),
            'depotLabel' => UserAccess::depotLabel($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        Client::create([
            'date_fiche' => now()->toDateString(),
            'ref_client' => Client::nextRef(),
            'nom_client' => $data['nom_client'],
            'nom_gerant' => $data['nom_gerant'] ?? null,
            'contact' => $data['contact'] ?? null,
            'ville' => $data['ville'] ?? null,
            'type_reglement' => $data['type_reglement'] ?? null,
            'banque' => $data['banque'] ?? null,
            'rib' => $data['rib'] ?? null,
            'depot' => $depotKey,
            'user_id' => $depotKey ? $user->id : null,
        ]);

        Ville::syncFrom($data['ville'] ?? null);
        Reglement::syncFrom(TypesReglement::label($data['type_reglement'] ?? null));

        return redirect()->route('clients.fiche');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->assertAccess($client);
        $data = $this->validated($request, $client);

        $client->update([
            'nom_client' => $data['nom_client'],
            'nom_gerant' => $data['nom_gerant'] ?? null,
            'contact' => $data['contact'] ?? null,
            'ville' => $data['ville'] ?? null,
            'type_reglement' => $data['type_reglement'] ?? null,
            'banque' => $data['banque'] ?? null,
            'rib' => $data['rib'] ?? null,
        ]);

        Ville::syncFrom($data['ville'] ?? null);
        Reglement::syncFrom(TypesReglement::label($data['type_reglement'] ?? null));

        return redirect()->route('clients.fiche');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->assertAccess($client);
        $client->delete();

        return redirect()->route('clients.fiche');
    }

    private function assertAccess(Client $client): void
    {
        $user = auth()->user();
        $depotKey = UserAccess::depotKey($user);

        if ($depotKey && ($client->depot !== $depotKey || (int) $client->user_id !== (int) $user->id)) {
            abort(403, 'Ce client n\'appartient pas à votre dépôt.');
        }
    }

    private function validated(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'nom_client' => ['required', 'string', 'max:255'],
            'nom_gerant' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'ville' => ['nullable', 'string', 'max:100'],
            'type_reglement' => ['nullable', 'string', Rule::in(array_keys(TypesReglement::options()))],
            'banque' => ['nullable', 'string', 'max:100'],
            'rib' => ['nullable', 'digits:24'],
        ], [
            'nom_client.required' => 'Le nom du client est obligatoire.',
            'rib.digits' => 'Le RIB doit contenir exactement 24 chiffres.',
        ]);
    }
}
