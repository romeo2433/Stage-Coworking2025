<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('utilisateur')) {
            return redirect()->route('connexion.create')
                             ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }
        return $next($request);
    }
}
