<?php

namespace App\Http\Middleware;

use App\Support\UserAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthorizedAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $route = $request->route()?->getName();

        if (! $route || UserAccess::canAccessRoute($user, $route)) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé pour votre dépôt / profil.');
    }
}
