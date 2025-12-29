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
                ->orderBy('date_debut', 'desc')
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
            $reservation->update(['Statut_Reservation' => 'terminee']);
        
            return redirect()->back()->with('success', 'Check-out effectué avec succès.');
        }
        public function heuresDisponibles($id, Request $request)
        {
            $date = $request->query('date');
            if (!$date) {
                return response()->json(['error' => 'Date manquante'], 400);
            }
        
            $espace = Espace::findOrFail($id);
            $quantiteTotale = $espace->quantite ?? 1;
        
            $heuresPossibles = [];
            for ($h = 9; $h < 17; $h++) {
                $heuresPossibles[] = sprintf('%02d:00', $h);
            }
        
            $startOfDay = Carbon::parse("$date 00:00:00");
            $endOfDay   = Carbon::parse("$date 23:59:59");
        
            $reservations = Reservation::where('Id_Espace', $id)
                ->where('date_debut', '<', $endOfDay)
                ->where('date_fin', '>', $startOfDay)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','payee'])
                ->get();
            $result = [];        
            foreach ($heuresPossibles as $heureStr) {
                $heureDebut = Carbon::parse("$date $heureStr");
                $heureFin = $heureDebut->copy()->addHour();
            
                $placesOccupees = 0;
            
                foreach ($reservations as $r) {
                    $debut = Carbon::parse($r->date_debut);
                    $fin = Carbon::parse($r->date_fin);
            
                    // Chevauchement : une réservation occupe l'heure si elle commence avant la fin de l'heure
                    // ET finit après le début de l'heure
                    if ($debut < $heureFin && $fin > $heureDebut) {
                        $placesOccupees += $r->quantite_reservation;
                    }
                }
            
                $placesRestantes = max(0, $quantiteTotale - $placesOccupees);
                $complet = $placesOccupees >= $quantiteTotale;
            
                $result[] = [
                    'heure'            => $heureStr,
                    'statut'           => $complet ? 'Réservée' : 'Disponible',
                    'label'            => $complet 
                        ? 'Réservée (complet)' 
                        : "Disponible ($placesRestantes place(s) restante(s))",
                    'places_restantes' => $placesRestantes
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
                    $client = $r->utilisateur ? $r->utilisateur->Nom . ' ' . $r->utilisateur->Prenom : 'Client inconnu';
                    $telephone = $r->utilisateur ? $r->utilisateur->numero : 'Non renseigné';
                
                    return [
                        'id' => $r->Id_Reservation, // utile si tu veux faire des actions plus tard
                        'title' => $espace . ' - ' . $client,
                        'start' => $r->date_debut,
                        'end' => $r->date_fin,
                        'backgroundColor' => '#4e73df',
                        'borderColor' => '#2e59d9',
                        'textColor' => '#fff',
                
                        // On ajoute toutes les infos qu'on veut afficher dans la modale
                        'extendedProps' => [
                            'client' => $client,
                            'telephone' => $telephone,
                            'email' => $r->utilisateur?->email ?? 'Non renseigné',
                            'espace' => $espace,
                            'date_debut' => \Carbon\Carbon::parse($r->date_debut)->format('d/m/Y H:i'),
                            'date_fin' => \Carbon\Carbon::parse($r->date_fin)->format('d/m/Y H:i'),
                            'statut' => $r->Statut_Reservation ?? 'Non défini',
                            // Ajoute ici tout autre champ que tu veux afficher
                        ]
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
