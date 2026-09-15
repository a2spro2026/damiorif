<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse|Response
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->session()->forget('_old_input');

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'statut' => ['required', 'string', 'in:directeur,gerant,facturation,magasinier,depot_tanger,depot_nador,depot_tetouan,depot_houcima,depot_belkciri,depot_damiorif'],
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'statut.required' => 'Veuillez sélectionner un statut.',
            'login.required' => 'Veuillez saisir votre identifiant.',
            'password.required' => 'Veuillez saisir votre mot de passe.',
        ]);

        $user = User::query()
            ->where('username', $credentials['login'])
            ->where('statut', $credentials['statut'])
            ->first();

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $credentials['password']])) {
            return back()->withErrors(['login' => 'Identifiants incorrects ou statut invalide.']);
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
