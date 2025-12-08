<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Espace;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Liste des réservations en attente
    public function index(Request $request)
    {
        $espaces = Espace::orderBy('Nom')->get();
        $query = Reservation::with('utilisateur', 'espace')
            ->whereNot('Statut_Reservation', 'en_attente');
            
    
        // Filtres sur utilisateur, espace, dates, statut
        if ($request->filled('utilisateur')) {
            $query->whereHas('utilisateur', function ($q) use ($request): void {
                $q->where('Prenom', 'like', '%' . $request->utilisateur . '%')
                  ->orWhere('Nom', 'like', '%' . $request->utilisateur . '%');
            });
        }
        if ($request->filled('espace')) {
            $query->whereHas('espace', function ($q) use ($request): void {
                $q->where('Id_Espace', $request->espace);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_fin', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('Statut_Reservation', $request->statut);
        }
    
        $historique = $query->orderBy('date_debut', 'desc')
        ->paginate(10)
        ->withQueryString();
    
        // Réservations en attente
        $reservations = Reservation::with('utilisateur', 'espace')
            ->where('Statut_Reservation', 'en_attente')
            ->get();
    
        return view('admin.reservations.index', compact('reservations', 'historique', 'espaces'));
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
