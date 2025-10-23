<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Espace;
use Illuminate\Http\Request;

class ReservationApiController extends Controller
{
    public function index()
{
    // Récupère toutes les réservations sauf celles annulées
    $reservations = Reservation::with('espace.typeEspace')
                    ->where('Statut_Reservation', '!=', 'annulée')
                    ->get();

    return $reservations->map(function ($r) {
        return [
            'title' => $r->espace->Nom . ' (' . $r->espace->typeEspace->Type_Espace . ')',
            'start' => $r->date_debut,
            'end'   => $r->date_fin,
            'color' => '#28a745', 
        ];
    });
}

    

    public function espacesDisponibles(Request $request)
    {
        $date = $request->get('date');

        $espacesOccupes = Reservation::whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->pluck('Id_Espace');

        $espacesLibres = Espace::whereNotIn('Id_Espace', $espacesOccupes)
            ->pluck('Nom_Espace');

        return response()->json($espacesLibres);
    }
}
