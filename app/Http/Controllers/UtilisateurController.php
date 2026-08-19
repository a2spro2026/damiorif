<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AppMenus;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UtilisateurController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderByDesc('id')->get();

        return view('configuration.utilisateurs.index', [
            'users' => $users,
            'statuts' => AppMenus::statutOptions(),
            'autorisations' => AppMenus::autorisations(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        User::create([
            'name' => $data['name'],
            'cin' => $data['cin'] ?? null,
            'contact' => $data['contact'] ?? null,
            'username' => $data['username'],
            'email' => $data['username'].'@damiorif.local',
            'password' => $data['password'],
            'mot_de_passe' => $data['password'],
            'statut' => $data['statut'],
            'autorisations' => $this->resolveAutorisations($data['statut'], $data['autorisations'] ?? []),
        ]);

        return redirect()
            ->route('configuration.utilisateurs.index')
            ->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $this->validated($request, $utilisateur);

        $payload = [
            'name' => $data['name'],
            'cin' => $data['cin'] ?? null,
            'contact' => $data['contact'] ?? null,
            'username' => $data['username'],
            'statut' => $data['statut'],
            'autorisations' => $this->resolveAutorisations($data['statut'], $data['autorisations'] ?? []),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
            $payload['mot_de_passe'] = $data['password'];
        }

        $utilisateur->update($payload);

        return redirect()
            ->route('configuration.utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $utilisateur): RedirectResponse
    {
        if ($utilisateur->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $utilisateur->delete();

        return redirect()
            ->route('configuration.utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * @param  list<string>  $requested
     * @return list<string>
     */
    private function resolveAutorisations(string $statut, array $requested): array
    {
        if (str_starts_with($statut, 'depot_')) {
            return UserAccess::depotMenuKeys();
        }

        return array_values($requested);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $permissionKeys = array_merge(AppMenus::allPermissionKeys(), ['stock.fiche_produit']);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cin' => ['nullable', 'string', 'max:50'],
            'contact' => ['nullable', 'string', 'max:50'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:4', 'max:100'],
            'statut' => ['required', 'string', Rule::in(array_keys(AppMenus::statutOptions()))],
            'autorisations' => ['nullable', 'array'],
            'autorisations.*' => ['string', Rule::in($permissionKeys)],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'username.required' => 'Le login est obligatoire.',
            'username.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
        ]);
    }
}
