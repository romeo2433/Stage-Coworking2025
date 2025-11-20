<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Mode;


class AdminReservationController extends Controller
{
    /**
     * Affiche les réservations confirmées et gère la recherche par référence.
     */
    public function montre(Request $request)
    {
        $modes = Mode::all();
        $query = Reservation::with('utilisateur', 'espace')
            ->where('Statut_Reservation', 'confirmee');
    
        // Recherche par référence
        if ($request->filled('reference')) {
            $query->where('Reference', 'like', '%' . $request->reference . '%');
        }
    
        // Recherche par nom/prénom du client
        if ($request->filled('nom_utilisateur')) {
            $query->whereHas('utilisateur', function ($q) use ($request) {
                $q->where('Nom', 'like', '%' . $request->nom_utilisateur . '%')
                  ->orWhere('Prenom', 'like', '%' . $request->nom_utilisateur . '%');
            });
        }
    
        $reservations = $query->orderBy('created_at', 'desc')->get();
    
        return view('admin.reservations.montre', compact('reservations', 'modes'));
    }
    

}
