<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function fournisseurs(): View
    {
        return view('pages.placeholder', ['title' => 'Fournisseurs']);
    }

    public function stock(): View
    {
        return view('pages.placeholder', ['title' => 'Stock']);
    }

    public function clients(): View
    {
        return view('pages.placeholder', ['title' => 'Client']);
    }

    public function charges(): View
    {
        return view('pages.placeholder', ['title' => 'Charges']);
    }

    public function rapports(): View
    {
        return view('pages.placeholder', ['title' => 'Rapports']);
    }

    public function configuration(): View
    {
        return view('pages.placeholder', ['title' => 'Configuration']);
    }
}
