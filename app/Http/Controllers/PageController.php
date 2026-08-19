<?php

namespace App\Http\Controllers;

use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $title): View
    {
        return view('pages.placeholder', [
            'title' => $title,
        ]);
    }

    public function configuration(): RedirectResponse
    {
        return redirect()->route('configuration.utilisateurs.index');
    }

    public function moduleHome(string $module): RedirectResponse
    {
        $nav = UserAccess::navigationFor(auth()->user())[$module] ?? null;
        if (! $nav || empty($nav['children'])) {
            return redirect()->route('dashboard');
        }

        return redirect()->route($nav['children'][0]['route']);
    }
}
