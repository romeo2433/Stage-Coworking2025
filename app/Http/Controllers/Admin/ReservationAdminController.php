<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Equipement;
use App\Models\Utilisateur;
use App\Models\User;

class ReservationAdminController extends Controller
{
    public function create()
        {
            $espaces = Espace::with('equipements')->get();
            $utilisateurs = Utilisateur::all();
            return view('admin.reservations.create', compact('espaces','utilisateurs'));
        }

    public function store(Request $request)
        {
            $request->validate([
                'Id_Espace' => 'required|exists:espaces,Id_Espace',
                'email_client' => 'required|email|exists:utilisateurs,email',
                'date_debut' => 'required|date',
                'heure_debut' => 'required',
                'duree' => 'required|numeric|min:1',
                'equipements' => 'array'
            ]);
        
            // Récupération de l'utilisateur et de l'espace
            $utilisateur = Utilisateur::where('email', $request->email_client)->first();
            $espace = Espace::findOrFail($request->Id_Espace);
        
            // Calcul des dates
            $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
            $fin = (clone $debut)->add(new \DateInterval('PT'.$request->duree.'H'));
        
            // Vérifier si l'espace est déjà réservé
            $existing = Reservation::where('Id_Espace', $espace->Id_Espace)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee'])
                ->where(function($q) use ($debut, $fin){
                    $q->where('date_debut','<',$fin)->where('date_fin','>',$debut);
                })->exists();
        
            if ($existing) {
                return back()->withErrors(['date_debut' => 'Cet espace est déjà réservé sur ce créneau.'])->withInput();
            }
        
            // Calcul du total
            $total = $espace->tarif_horaire * $request->duree;
            if($request->has('equipements')){
                foreach($request->equipements as $idEquip){
                    $equipement = Equipement::find($idEquip);
                    if($equipement) $total += $equipement->prix;
                }
            }
            // Récupérer le dernier numéro utilisé dans la référence
            $lastReference = Reservation::where('Reference', 'like', 'RES-%')
                ->orderBy('Id_Reservation', 'desc')
                ->pluck('Reference')
                ->first();

            if ($lastReference) {
                $lastNumber = (int) substr($lastReference, 4); 
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $reference = 'RES-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Création de la réservation
            $reservation = Reservation::create([
                'Reference' => $reference,
                'Id_Espace' => $request->Id_Espace,
                'Id_Utilisateur' => $utilisateur->Id_Utilisateur, 
                'date_debut' => $debut,
                'date_fin' => $fin,
                'duree_heures' => $request->duree,              
                'total' => $total,                              
                'Statut_Reservation' => 'confirmee',            
                'Id_Type' => 1                                  
            ]);
        
            // Association des équipements
            if($request->has('equipements')){
                $reservation->equipements()->sync($request->equipements);
            }
        
            return redirect()->route('admin.reservations.montre')
                             ->with('success', 'Réservation créée avec succès. Réf : ' . $reference);
        }
    
    public function espaceDetails($id)
        {
            $espace = Espace::with('equipements')->find($id);

            if (!$espace) {
                return response()->json(['error' => 'Espace introuvable'], 404);
            }

            return response()->json([
                'tarif_horaire' => $espace->tarif_horaire,
                'equipements' => $espace->equipements->map(function($e){
                    return [
                        'Id_Equipement' => $e->Id_Equipement,
                        'nom' => $e->nom,
                        'prix' => $e->prix
                    ];
                })
            ]);
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
        
            // Récupérer toutes les réservations qui bloquent ce créneau
            $reservations = Reservation::where('Id_Espace', $id)
                ->whereDate('date_debut', $date)
                ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee']) // ✅ ajouté "terminee"
                ->get(['date_debut', 'date_fin']);
        
            $heuresOccupees = [];
        
            foreach ($reservations as $r) {
                $hDebut = (int)date('H', strtotime($r->date_debut));
                $hFin = (int)date('H', strtotime($r->date_fin));
        
                // Exemple : 9h-11h → 9:00 et 10:00 sont occupées
                for ($h = $hDebut; $h < $hFin; $h++) {
                    $heuresOccupees[] = sprintf('%02d:00', $h);
                }
            }
        
            // Calcul des heures libres
            $heuresDisponibles = array_values(array_diff($heuresPossibles, $heuresOccupees));
        
            return response()->json(['disponibles' => $heuresDisponibles]);
        }
        
        

}
