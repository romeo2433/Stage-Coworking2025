<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use App\Models\Equipement;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function create($id)
    {
        $espace = Espace::findOrFail($id);
        // On récupère les équipements liés au type de l'espace
        $equipements = $espace->typeEspace->equipements ?? [];
        return view('reservations.create', compact('espace', 'equipements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Id_Espace' => 'required|exists:espaces,Id_Espace',
            'date_debut' => 'required|date',
            'heure_debut' => 'required',
            'duree' => 'required|numeric|min:1',
            'equipements' => 'array'
        ]);
    
        $espace = Espace::findOrFail($request->Id_Espace);
        $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
        $duree = $request->duree;
        $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));
    
        // Vérifier si l'espace est déjà réservé sur ce créneau
        $existing = Reservation::where('Id_Espace', $espace->Id_Espace)
            ->whereIn('Statut_Reservation', ['en_attente', 'confirmee','terminee'])
            ->where(function($query) use ($debut, $fin) {
                $query->where('date_debut', '<', $fin)  
                      ->where('date_fin', '>', $debut); 
            })
            ->exists();
    
        if ($existing) {
            return redirect()->back()
                ->with('error', 'Cet espace est déjà réservé sur ce créneau. Veuillez choisir un autre horaire.');
        }
    
        // Calcul du total
        $total = $espace->tarif_horaire * $duree;
        $equipementsSelected = [];
    
        if($request->has('equipements')){
            foreach($request->equipements as $idEquip){
                $equipement = $espace->equipements()->find($idEquip);
                if($equipement){
                    $equipementsSelected[] = $equipement;
                    $total += $equipement->prix;
                }
            }
        }
    
        // Stocker dans la session pour prévisualisation
        session([
            'reservation_preview' => [
                'Id_Espace' => $espace->Id_Espace,
                'debut' => $debut->format('Y-m-d H:i:s'),
                'fin' => $fin->format('Y-m-d H:i:s'),
                'duree' => $duree,
                'equipements' => $request->equipements ?? [],
                'total' => $total
            ]
        ]);
    
        // Rediriger vers la prévisualisation
        return view('reservations.preview', compact('espace', 'debut', 'fin', 'duree', 'equipementsSelected', 'total'));
    }
    


    public function preview(Request $request)
    {
        $request->validate([
            'Id_Espace' => 'required|exists:espaces,Id_Espace',
            'date_debut' => 'required|date',
            'heure_debut' => 'required',
            'duree' => 'required|numeric|min:1',
            'equipements' => 'array'
        ]);

        $espace = Espace::findOrFail($request->Id_Espace);
        $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
        $duree = $request->duree;
        $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));

        $total = $espace->tarif_horaire * $duree;

        $equipementsSelected = [];
        if($request->has('equipements')){
            foreach($request->equipements as $idEquip){
                $equipement = $espace->equipements()->find($idEquip);
                if($equipement){
                    $equipementsSelected[] = $equipement;
                    $total += $equipement->prix;
                }
            }
        }

        // On stocke les données dans la session pour la confirmation
        session([
            'reservation_preview' => [
                'Id_Espace' => $espace->Id_Espace,
                'debut' => $debut->format('Y-m-d H:i:s'),
                'fin' => $fin->format('Y-m-d H:i:s'), 
                'duree' => $duree,
                'equipements' => $request->equipements ?? [],
                'total' => $total
            ]
        ]);               

        return view('reservations.preview', compact('espace', 'debut', 'fin', 'duree', 'equipementsSelected', 'total'));
    }

    public function confirm()
    {
        $data = session('reservation_preview');
    
        if(!$data){
            return redirect()->route('types_espaces.index')
                            ->with('error', 'Aucune réservation à confirmer.');
        }
    
        $espaceId = $data['Id_Espace'];
        $debut = new \DateTime($data['debut']);
        $fin = new \DateTime($data['fin']);
    
        // Vérifier si l'espace est déjà réservé sur ce créneau
        $existing = Reservation::where('Id_Espace', $espaceId)
    ->whereIn('Statut_Reservation', ['en_attente', 'confirmee','terminee'])
    ->where(function($query) use ($debut, $fin) {
        $query->where(function($q) use ($debut, $fin) {
            $q->where('date_debut', '<', $fin)
              ->where('date_fin', '>', $debut);
        });
    })
    ->exists();

    
        if ($existing) {
            return redirect()->route('types_espaces.index')
                ->with('error', 'Impossible de confirmer : cet espace est déjà réservé sur ce créneau.');
        }
    
        // Générer la référence
        $reference = 'RES-' . strtoupper(uniqid());
    
        // Créer la réservation
        $reservation = Reservation::create([
            'reference' => $reference,
            'Id_Espace' => $espaceId,
            'date_debut' => $data['debut'],
            'date_fin' => $data['fin'],
            'duree_heures' => $data['duree'],
            'total' => $data['total'],
            'Statut_Reservation' => 'en_attente',
            'Id_Utilisateur' => session('utilisateur.Id_Utilisateur') ?? 1,
            'Id_Type' => 1,
        ]);
    
        // Attacher les équipements
        if(!empty($data['equipements'])){
            foreach($data['equipements'] as $idEquip){
                $reservation->equipements()->attach($idEquip, ['Nombre_Ajout' => 1]);
            }
        }
    
        // Supprimer la session preview
        session()->forget('reservation_preview');
    
        return redirect()->route('types_espaces.index')
                        ->with('success', "Réservation confirmée avec succès. Réf: $reference, Total: ".$data['total']." Ar");
    }
    
    public function myReservations()
    {
        $userId = session('utilisateur')->Id_Utilisateur;

        $reservations = Reservation::with('espace', 'equipements')
            ->where('Id_Utilisateur', $userId)
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('reservations.my_reservations', compact('reservations'));
    }
    public function getDisponibilites($id, Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['error' => 'Date manquante'], 400);
        }

        $espace = Espace::findOrFail($id);

        // Créneaux possibles (par exemple 9h → 17h)
        $allSlots = [];
        for ($h = 9; $h < 18; $h++) {
            $allSlots[] = sprintf('%02d:00', $h);
        }

        // Récupère les réservations existantes ce jour-là
        $reservations = Reservation::where('Id_Espace', $id)
            ->whereDate('date_debut', $date)
            ->whereIn('Statut_Reservation', ['en_attente', 'confirmee' ,'terminee' , 'partiellement_payee' ,'en_attente_de_paiement'])
            ->get(['date_debut', 'date_fin']);

        // Supprime les heures déjà prises
        $takenSlots = [];
        foreach ($reservations as $r) {
            $start = (int) date('H', strtotime($r->date_debut));
            $end = (int) date('H', strtotime($r->date_fin));
            for ($h = $start; $h < $end; $h++) {
                $takenSlots[] = sprintf('%02d:00', $h);
            }
        }

        $availableSlots = array_values(array_diff($allSlots, $takenSlots));

        return response()->json([
            'date' => $date,
            'availableSlots' => $availableSlots,
        ]);
    }

}
