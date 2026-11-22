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
        $users = User::query()
            ->when(
                ! $this->isDeveloper(auth()->user()),
                fn ($q) => $q->where('username', '!=', self::developerUsername())
            )
            ->orderByDesc('id')
            ->get();

        return view('configuration.utilisateurs.index', [
            'users' => $users,
            'statuts' => AppMenus::statutOptions(),
            'autorisations' => AppMenus::autorisations(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (strcasecmp((string) $data['username'], self::developerUsername()) === 0) {
            return back()->withErrors(['username' => 'Ce login est réservé.']);
        }

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
        if ($this->isProtectedDeveloper($utilisateur) && ! $this->isDeveloper(auth()->user())) {
            return back()->withErrors(['update' => 'Ce compte développeur est réservé et ne peut pas être modifié.']);
        }

        $data = $this->validated($request, $utilisateur);

        if ($this->isProtectedDeveloper($utilisateur)) {
            $data['username'] = self::developerUsername();
            $data['statut'] = 'directeur';
        }

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

        if ($this->isProtectedDeveloper($utilisateur)) {
            return back()->withErrors(['delete' => 'Ce compte développeur est réservé et ne peut pas être supprimé.']);
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

    private static function developerUsername(): string
    {
        return 'zerragui';
    }

    private function isProtectedDeveloper(User $user): bool
    {
        return strcasecmp((string) $user->username, self::developerUsername()) === 0;
    }

    private function isDeveloper(?User $user): bool
    {
        return $user !== null && $this->isProtectedDeveloper($user);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $permissionKeys = array_merge(AppMenus::allPermissionKeys(), ['stock.fiche_produit']);

        $data = $request->validate([
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
            'autorisations.*' => ['string'],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'username.required' => 'Le login est obligatoire.',
            'username.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
        ]);

        $allowed = array_flip($permissionKeys);
        $data['autorisations'] = array_values(array_filter(
            $data['autorisations'] ?? [],
            fn ($key) => isset($allowed[$key])
        ));

        return $data;
    }
}
