<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Models\Checkin;
use Illuminate\Http\Request;
use App\Models\Espace;
use Illuminate\Support\Facades\Log;



class PlanningController extends Controller
{
    public function index()
        {
            $today = Carbon::today()->toDateString();
        
            //  Récupère toutes les réservations (pas que celles du jour)
            $reservations = Reservation::with(['espace', 'utilisateur'])
                ->orderBy('date_debut', 'asc')
                ->get();
             
            //  Réservations du jour (pour le tableau)
            $todayCarbon = Carbon::today();
            $reservationsDuJour = $reservations->filter(function ($r) use ($todayCarbon) {
                return Carbon::parse($r->date_debut)->isSameDay($todayCarbon);
            });
            //  Envoie les données à la vue
            return view('admin.planning.profil', [
                'reservations' => $reservationsDuJour,
                'today' => $today
            ]);
        }
        

    public function checkin($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);
            // Vérifie si la réservation est dans un statut autorisé
            if (!in_array($reservation->Statut_Reservation, [
                'en_attente', 'confirmee', 'partiellement_payee', 'en_attente_de_paiement', 'payee'
            ])) {
                return redirect()->back()->with('error', 'Impossible de faire le check-in pour cette réservation.');
            }
            // Vérifie si un check-in existe déjà pour cette réservation
            $checkin = Checkin::where('Id_Reservation', $reservation->Id_Reservation)->first();
            if ($checkin) {
                //  Si déjà existant → mettre à jour l’heure d’arrivée
                $checkin->update([
                    'heure_arrivee' => now()->format('H:i:s'),
                ]);
                $message = 'Heure d’arrivée mise à jour avec succès.';
            } else {
                // Sinon, créer un nouveau check-in
                Checkin::create([
                    'Id_Reservation' => $reservation->Id_Reservation,
                    'heure_arrivee' => now()->format('H:i:s'),
                ]);
                $message = 'Check-in effectué avec succès.';
            }

            return redirect()->back()->with('success', $message);
        }

    public function checkout($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);
        
            // Vérifier si la réservation est payée
            if($reservation->Statut_Reservation !== 'payee'){
                return redirect()->back()->with('error', 'Impossible de faire le check-out : réservation non encore payée.');
            }
        
            // Trouver le check-in non terminé
            $checkin = Checkin::where('Id_Reservation', $reservation->Id_Reservation)
                                ->whereNull('heure_sortie')
                                ->first();
        
            if(!$checkin){
                return redirect()->back()->with('error', 'Aucun check-in actif pour cette réservation.');
            }
        
            // Mettre à jour l'heure de sortie
            $checkin->update(['heure_sortie' => now()->format('H:i:s')]);
        
            // Mettre à jour le statut de la réservation
            $reservation->update(['Statut_Reservation' => 'payee','terminee','confirmee']);
        
            return redirect()->back()->with('success', 'Check-out effectué avec succès.');
        }
        public function heuresDisponibles($id, Request $request)
        {
            $date = $request->query('date');
            if (!$date) {
                return response()->json(['error' => 'Date manquante'], 400);
            }
        
            // Heures possibles : 9h à 17h
            $heuresPossibles = [];
            for ($h = 9; $h < 17; $h++) {
                $heuresPossibles[] = sprintf('%02d:00', $h);
            }
        
            // Récupérer toutes les réservations pour cette date et cet espace
            $reservations = Reservation::where('Id_Espace', $id)
                ->where(function($query) use ($date) {
                    $query->whereDate('date_debut', $date)
                          ->orWhereDate('date_fin', $date);
                })
                ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee','payee'])
                ->get(['date_debut', 'date_fin']);
        
            // DEBUG: Vérifiez ce qui est récupéré
            Log::info('Réservations trouvées pour espace ' . $id . ' à la date ' . $date . ':', $reservations->toArray());
        
            // Tableau [heure => statut]
            $heuresStatut = [];
            foreach ($heuresPossibles as $h) {
                $heuresStatut[$h] = 'Disponible';
            }
        
            foreach ($reservations as $reservation) {
                // Convertir en objets DateTime pour mieux gérer
                $debut = Carbon::parse($reservation->date_debut);
                $fin = Carbon::parse($reservation->date_fin);
                
                // Vérifier chaque heure possible
                foreach ($heuresPossibles as $heureStr) {
                    $heureDebut = Carbon::parse($date . ' ' . $heureStr);
                    $heureFin = Carbon::parse($date . ' ' . $heureStr)->addHour();
                    
                    // Vérifier si la réservation chevauche cette heure
                    if ($debut < $heureFin && $fin > $heureDebut) {
                        $heuresStatut[$heureStr] = 'Réservée';
                        Log::info('Heure ' . $heureStr . ' marquée comme réservée par réservation: ' . $debut . ' - ' . $fin);
                    }
                }
            }
        
            // DEBUG: Vérifiez le résultat final
            Log::info('Statut final des heures:', $heuresStatut);
        
            // Conversion en liste d'objets pour le frontend
            $result = [];
            foreach ($heuresStatut as $heure => $statut) {
                $result[] = [
                    'heure' => $heure,
                    'statut' => $statut,
                ];
            }
        
            return response()->json($result);
        }
        
        public function calendar(Request $request)
        {
            $espaces = Espace::orderBy('Nom')->get();
        
            $selectedEspace = $request->espace ?? '';
            $selectedDate = $request->date ?? '';
            $selectedHeure = $request->heure ?? null;
        
            $disponibilite = null;
            $toutesLesHeures = [];
        
            // Si l'utilisateur a choisi un espace et une date
            if ($selectedEspace && $selectedDate) {
                try {
                    $response = $this->heuresDisponibles($selectedEspace, $request);
                    $toutesLesHeures = json_decode($response->getContent(), true);
                    
                    if ($selectedHeure) {
                        // Trouver le statut de l'heure sélectionnée
                        foreach ($toutesLesHeures as $h) {
                            if ($h['heure'] === $selectedHeure) {
                                $disponibilite = $h['statut'];
                                break;
                            }
                        }
                    } else {
                        // Compter les heures disponibles
                        $compteurDispo = 0;
                        foreach ($toutesLesHeures as $h) {
                            if ($h['statut'] === 'Disponible') {
                                $compteurDispo++;
                            }
                        }
                        $disponibilite = $compteurDispo > 0 ? $compteurDispo . ' créneau(x) disponible(s)' : 'Complet';
                    }
                } catch (\Exception $e) {
                    Log::error('Erreur dans calendar: ' . $e->getMessage());
                    $disponibilite = 'Erreur de calcul';
                }
            }
        
            // Récupérer toutes les réservations pour le calendrier
            $reservations = Reservation::with(['espace', 'utilisateur'])
                ->orderBy('date_debut')
                ->get();
        
            $events = $reservations->map(function ($r) {
                $espace = $r->espace ? $r->espace->Nom : 'Espace inconnu';
                $client = $r->utilisateur ? $r->utilisateur->nom : 'Client inconnu';
        
                return [
                    'title' => $client . ' - ' . $espace,
                    'start' => $r->date_debut,
                    'end' => $r->date_fin,
                    'backgroundColor' => '#4e73df',
                    'borderColor' => '#2e59d9',
                    'textColor' => '#fff',
                ];
            });
        
            return view('admin.planning.calendar', [
                'events' => $events,
                'espaces' => $espaces,
                'selectedEspace' => $selectedEspace,
                'selectedDate' => $selectedDate,
                'selectedHeure' => $selectedHeure,
                'disponibilite' => $disponibilite,
                'toutesLesHeures' => $toutesLesHeures,
            ]);
        }
        
}
