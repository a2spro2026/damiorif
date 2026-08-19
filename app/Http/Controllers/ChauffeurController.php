<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChauffeurController extends Controller
{
    public function index(): View
    {
        return view('configuration.parametres.chauffeurs', [
            'chauffeurs' => Chauffeur::query()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Chauffeur::create($data);

        return redirect()->route('configuration.parametres.chauffeurs');
    }

    public function update(Request $request, Chauffeur $chauffeur): RedirectResponse
    {
        $chauffeur->update($this->validated($request));

        return redirect()->route('configuration.parametres.chauffeurs');
    }

    public function destroy(Chauffeur $chauffeur): RedirectResponse
    {
        $chauffeur->delete();

        return redirect()->route('configuration.parametres.chauffeurs');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'cin' => ['nullable', 'string', 'max:50'],
        ], [
            'nom.required' => 'Le nom du chauffeur est obligatoire.',
        ]);

        $data['nom'] = trim($data['nom']);
        $data['telephone'] = isset($data['telephone']) ? trim($data['telephone']) : null;
        $data['cin'] = isset($data['cin']) ? trim($data['cin']) : null;

        return $data;
    }
}
