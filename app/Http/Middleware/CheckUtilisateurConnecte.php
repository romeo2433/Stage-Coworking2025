<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUtilisateurConnecte
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie la session avec la clé correcte (minuscule)
        if (!session('utilisateur') || session('profil') != 4) {
            return redirect()->route('connexion.create')
                             ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        return $next($request);
    }
}
