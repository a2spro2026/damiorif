<?php

namespace App\Http\Controllers;

use App\Models\Tresorerie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TresorerieController extends Controller
{
    public function index(): View
    {
        return view('configuration.parametres.tresoreries', [
            'tresoreries' => Tresorerie::query()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:tresoreries,nom'],
        ], [
            'nom.required' => 'Le nom de la trésorerie est obligatoire.',
        ]);

        Tresorerie::create(['nom' => trim($data['nom'])]);

        return redirect()->route('configuration.parametres.tresoreries');
    }

    public function update(Request $request, Tresorerie $tresorerie): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:tresoreries,nom,'.$tresorerie->id],
        ]);

        $tresorerie->update(['nom' => trim($data['nom'])]);

        return redirect()->route('configuration.parametres.tresoreries');
    }

    public function destroy(Tresorerie $tresorerie): RedirectResponse
    {
        $tresorerie->delete();

        return redirect()->route('configuration.parametres.tresoreries');
    }
}
