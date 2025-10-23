<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Liste des réservations en attente
    public function index()
    {
        // Réservations en attente
        $reservations = Reservation::with('utilisateur', 'espace')
            ->where('Statut_Reservation', 'en_attente')
            ->get();

        // Historique : toutes les réservations qui ne sont pas en attente
        $historique = Reservation::with('utilisateur', 'espace')
            ->whereNot('Statut_Reservation', 'en_attente')
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('admin.reservations.index', compact('reservations', 'historique'));
    }


    // Confirmer une réservation
    public function confirm(Reservation $reservation)
    {
        $reservation->Statut_Reservation = 'confirmee';
        $reservation->save();

        return redirect()->back()->with('success', 'Réservation confirmée.');
    }

    // Rejeter une réservation
    public function reject(Reservation $reservation)
    {
        $reservation->Statut_Reservation = 'annulee';
        $reservation->save();

        return redirect()->back()->with('error', 'Réservation rejetée.');
    }
}
