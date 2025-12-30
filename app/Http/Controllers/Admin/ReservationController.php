<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Espace;
use App\Models\Abonnement;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // Liste des réservations en attente
   public function index(Request $request)
    {
        $espaces = Espace::orderBy('Nom')->get();
        $query = Reservation::with('utilisateur', 'espace', 'createur')
            ->whereNot('Statut_Reservation', 'en_attente');
            
    
        // Filtres sur utilisateur, espace, dates, statut
        if ($request->filled('utilisateur')) {
            $query->whereHas('utilisateur', function ($q) use ($request): void {
                $q->where('Prenom', 'like', '%' . $request->utilisateur . '%')
                  ->orWhere('Nom', 'like', '%' . $request->utilisateur . '%');
            });
        }

         // Filtre sur créateur / réservé par
         if ($request->filled('reserver_par')) {
            $query->whereHas('createur', function ($q) use ($request) {
                // S'assurer que le créateur est profil 1 ou 3
                $q->whereIn('Id_Profil', [1, 3])
                  // Filtrer par l'Id de l'utilisateur sélectionné
                  ->where('Id_Utilisateur', $request->reserver_par);
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
        // Récupérer les utilisateurs qui peuvent réserver (profil 1 ou 3)
        $createurs = Utilisateur::whereIn('Id_Profil', [1, 3])
        ->orderBy('Prenom')
        ->get();

    
        $historique = $query->orderBy('date_debut', 'desc')
        ->paginate(10)
        ->withQueryString();
    
        // Réservations en attente
        $reservations = Reservation::with('utilisateur', 'espace')
            ->where('Statut_Reservation', 'en_attente')
            ->get();
    
        return view('admin.reservations.index', compact('reservations', 'historique', 'espaces', 'createurs'));
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
    
        if ($reservation->Statut_Reservation !== 'confirmee') {
            return $request->ajax()
                ? response()->json(['status'=>'error','message'=>'Impossible de modifier cette réservation.'])
                : back()->with('error','Impossible de modifier cette réservation.');
        }
    
        $request->validate([
            'duree' => 'required|integer|min:1',
        ]);
    
        $espace = $reservation->espace;
        $debut  = new \DateTime($reservation->date_debut);
        $quantite = $reservation->quantite_reservation ?? 1;
        $dureeDemandee = (int) $request->duree;
    
        $prochaineReservation = Reservation::where('Id_Espace', $reservation->Id_Espace)
            ->where('Statut_Reservation', 'confirmee')
            ->where('Id_Reservation', '!=', $reservation->Id_Reservation)
            ->where('date_debut', '>', $reservation->date_debut)
            ->orderBy('date_debut')
            ->first();
    
        if ($prochaineReservation) {
            $dateLimite = new \DateTime($prochaineReservation->date_debut);
            $dureeMax = floor(($dateLimite->getTimestamp() - $debut->getTimestamp()) / 3600);
            if ($dureeMax < 1) {
                return $request->ajax()
                    ? response()->json(['status'=>'error','message'=>'Aucune heure disponible après cette réservation.'])
                    : back()->with('error','Aucune heure disponible après cette réservation.');
            }
        } else {
            $dureeMax = $dureeDemandee;
        }
    
        if ($dureeDemandee > $dureeMax) {
            return $request->ajax()
                ? response()->json(['status'=>'error','message'=>"Durée trop longue. Maximum autorisé : {$dureeMax} heure(s)."])
                : back()->with('error',"Durée trop longue. Maximum autorisé : {$dureeMax} heure(s).");
        }
    
        $fin = (clone $debut)->add(new \DateInterval("PT{$dureeDemandee}H"));
        $total = ($espace->tarif_horaire * $dureeDemandee) * $quantite;
    
        $reservation->duree_heures = $dureeDemandee;
        $reservation->date_fin = $fin->format('Y-m-d H:i:s');
        $reservation->total = $total;
        $reservation->save();
    
        return $request->ajax()
            ? response()->json(['status'=>'success','message'=>'Durée mise à jour avec succès.'])
            : back()->with('success','Durée mise à jour avec succès.');
    }
    public function showAjax($id)
        {
            $reservation = Reservation::with('utilisateur', 'espace', 'equipements')->find($id);

            if (!$reservation) {
                return response()->json(['error' => 'Réservation non trouvée'], 404);
            }

            // On peut renvoyer une vue partielle
            return view('admin.reservations.partials.reservation_details', compact('reservation'));
        }
        public function annuler(Request $request, $id)
        {
            // Validation : observation obligatoire
            $request->validate([
                'observation' => 'required|string|max:2000',
            ]);
        
            $reservation = Reservation::findOrFail($id);
        
            // Vérification du statut uniquement
            if ($reservation->Statut_Reservation !== 'confirmee') {
                return redirect()->back()->with('error', 'Seule une réservation confirmée peut être annulée.');
            }
        
            // Annulation par l'utilisateur connecté
            $reservation->Statut_Reservation = 'annulee';
            $reservation->observation = $request->input('observation');
            $reservation->created_by = session('utilisateur')->Id_Utilisateur; 
            $reservation->save();
        
            return redirect()->back()->with('success', 'Réservation annulée avec succès.');
        }
        
        
        
public function detruire($id)
{
    $reservation = Reservation::findOrFail($id);

    if ($reservation->Statut_Reservation !== 'confirmee') {
        return redirect()->back()->with('error', 'Seule une réservation confirmée peut être supprimée.');
    }

    $reservation->delete(); // ou forceDelete()

    return redirect()->back()->with('success', 'Réservation supprimée avec succès.');
}
    
    
    



}
