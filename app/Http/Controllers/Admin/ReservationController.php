<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Espace;
use App\Models\Abonnement;
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

    public function updateDuree(Request $request, $id)
        {
            $reservation = Reservation::findOrFail($id);

            // Vérifier si modifiable
            if (!in_array($reservation->Statut_Reservation, ['confirmee', 'payee'])) {
                return back()->with('error', 'Impossible de modifier cette réservation.');
            }

            $request->validate([
                'duree' => 'required|numeric|min:1',
            ]);

            $espace = $reservation->espace;
            $debut  = new \DateTime($reservation->date_debut);
            $quantite = $reservation->quantite_reservation ?? 1;

            $duree = $request->duree;

            // ----- RESEAU ABONNEMENT -----
            if ($request->Id_Abonnement) {

                $abo = Abonnement::find($request->Id_Abonnement);

                if ($abo && $abo->Type_Abonnement === 'journalier') {
                    $reservation->duree_heures = $duree;
                    $fin = (clone $debut)->add(new \DateInterval("P{$duree}D"));
                    $total = ($espace->tarif_journalier * $duree) * $quantite;

                } elseif ($abo && $abo->Type_Abonnement === 'mensuel') {
                    $reservation->duree_heures = $duree;
                    $fin = (clone $debut)->add(new \DateInterval("P{$duree}M"));
                    $total = ($espace->tarif_mensuel * $duree) * $quantite;

                } else {
                    // Abonnement mais pas reconnu → horaire par défaut
                    $reservation->duree_heures = $duree;
                    $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
                    $total = ($espace->tarif_horaire * $duree) * $quantite;
                }

            } else {
                // ----- SANS ABONNEMENT -----
                $reservation->duree_heures = $duree;
                $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
                $total = ($espace->tarif_horaire * $duree) * $quantite;
            }

            // Mise à jour finale
            $reservation->date_fin = $fin;
            $reservation->total = $total;
            $reservation->save();

            return back()->with('success', 'Durée et prix mis à jour avec succès.');
        }


}
