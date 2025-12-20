<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('profil')) {
            return redirect()->route('login');
        }

        // SEUL le vrai admin (Id_Profil = 1)
        if (session('profil') != 1) {
            abort(403, 'Accès réservé au super administrateur.');
        }

        return $next($request);
    }
}
