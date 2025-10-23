<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
        {
            if (session('profil') != 1) {
                return redirect()->route('connexion.create')->with('error', 'Accès refusé.');
            }
            return $next($request);
        }

}
