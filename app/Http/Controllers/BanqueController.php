<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BanqueController extends Controller
{
    public function index(): View
    {
        $this->syncExisting();

        $banques = Banque::query()->orderBy('nom')->get();

        return view('configuration.parametres.banque', [
            'banques' => $banques,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
        ], [
            'nom.required' => 'Le nom de la banque est obligatoire.',
        ]);

        Banque::syncFrom($data['nom']);

        return redirect()->route('configuration.parametres.banque');
    }

    public function update(Request $request, Banque $banque): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
        ]);

        $banque->update(['nom' => trim($data['nom'])]);

        return redirect()->route('configuration.parametres.banque');
    }

    public function destroy(Banque $banque): RedirectResponse
    {
        $banque->delete();

        return redirect()->route('configuration.parametres.banque');
    }

    private function syncExisting(): void
    {
        $noms = DB::table('fournisseurs')
            ->whereNotNull('banque')
            ->where('banque', '!=', '')
            ->pluck('banque')
            ->merge(
                DB::table('clients')
                    ->whereNotNull('banque')
                    ->where('banque', '!=', '')
                    ->pluck('banque')
            )
            ->unique(fn ($v) => mb_strtolower(trim($v)));

        foreach ($noms as $nom) {
            Banque::syncFrom($nom);
        }
    }
}
