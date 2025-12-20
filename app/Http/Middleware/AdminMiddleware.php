<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->has('profil')) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter.');
        }

        // Autoriser admin (1) ET super-admin / manager (3)
        if (!in_array(session('profil'), [1, 3])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }

}
