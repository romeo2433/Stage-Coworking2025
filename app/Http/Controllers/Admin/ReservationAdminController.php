<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Equipement;
use App\Models\Utilisateur;
use App\Models\Abonnement;

class ReservationAdminController extends Controller
{
    public function create(Request $request)
        {
            $espaces = Espace::with(['equipements' => function($query) {
                $query->where('Etat', 'OK');
            }])->where('Statut', 'disponible')->get();
        
            $utilisateurs = Utilisateur::all();
            $abonnements = Abonnement::where('Status_Abonnement', 'actif')->get();
        
            return view('admin.reservations.create', compact('espaces','utilisateurs','abonnements'));
        }
    
    

    public function store(Request $request)
    {
        $request->validate([
            'Id_Espace' => 'required|exists:espaces,Id_Espace',
            'email_client' => 'required|email|exists:utilisateurs,email',
            'date_debut' => 'required|date',
            'heure_debut' => 'required',
            'duree' => 'required|numeric|min:1',
            'duree_jour' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|numeric|min:0',
            'equipements' => 'array'
        ]);
    
        // Récupération de l'utilisateur et de l'espace
        $utilisateur = Utilisateur::where('email', $request->email_client)->first();
        $espace = Espace::findOrFail($request->Id_Espace);
    
        // Début de réservation
        $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
    
        // Calcul de la date de fin et total selon abonnement
        if ($request->Id_Abonnement) {
            $abo = Abonnement::find($request->Id_Abonnement);
            if ($abo->Type_Abonnement === 'journalier') {
                $duree = max(1, $request->duree_jour);
                $fin = (clone $debut)->add(new \DateInterval('P'.$duree.'D'));
                $total = $espace->tarif_journalier * $duree;
            } elseif ($abo->Type_Abonnement === 'mensuel') {
                $duree = max(1, $request->duree_mois);
                $fin = (clone $debut)->add(new \DateInterval('P'.$duree.'M'));
                $total = $espace->tarif_mensuel * $duree;
            } else { // horaire
                $duree = $request->duree;
                $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));
                $total = $espace->tarif_horaire * $duree;
            }
        } else { // pas d'abonnement → tarif horaire
            $duree = $request->duree;
            $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));
            $total = $espace->tarif_horaire * $duree;
        }
    
        // VÉRIFICATION DISPONIBILITÉ - AVEC MESSAGE D'ERREUR DÉTAILLÉ
        $reservationExistante = Reservation::where('Id_Espace', $espace->Id_Espace)
            ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee'])
            ->where(function($q) use ($debut, $fin){
                $q->where('date_debut','<',$fin)->where('date_fin','>',$debut);
            })->first();
    
        // Dans la méthode store, remplacez cette partie :
    if ($reservationExistante) {
                // Convertir les dates string en DateTime
                $debutExistante = new \DateTime($reservationExistante->date_debut);
                $finExistante = new \DateTime($reservationExistante->date_fin);
                
                // Message d'erreur détaillé
                $messageErreur = "Cet espace est déjà réservé du " . 
                                $debutExistante->format('d/m/Y H:i') . 
                                " au " . 
                                $finExistante->format('d/m/Y H:i') . 
                                " (Référence: " . $reservationExistante->Reference . ")";
                
                return redirect()->route('admin.reservations.create')
                                ->withErrors(['creneau_indisponible' => $messageErreur])
                                ->withInput();
            }
        
        // Ajouter le prix des équipements
        if($request->has('equipements')){
            foreach($request->equipements as $idEquip){
                $equipement = Equipement::find($idEquip);
                if($equipement) $total += $equipement->prix;
            }
        }
    
        // Génération référence
        $lastReference = Reservation::where('Reference', 'like', 'RES-%')
            ->orderBy('Id_Reservation', 'desc')
            ->pluck('Reference')
            ->first();
    
        $nextNumber = $lastReference ? ((int) substr($lastReference, 4) + 1) : 1;
        $reference = 'RES-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
        // Création réservation
        $reservation = Reservation::create([
            'Reference' => $reference,
            'Id_Espace' => $request->Id_Espace,
            'Id_Utilisateur' => $utilisateur->Id_Utilisateur,
            'Id_Abonnement' => $request->Id_Abonnement,
            'date_debut' => $debut,
            'date_fin' => $fin,
            'duree_heures' => $request->duree,
            'total' => $total,
            'Statut_Reservation' => 'confirmee',
            'Id_Type' => 1
        ]);
    
        // Association équipements
        if($request->has('equipements')){
            $reservation->equipements()->sync($request->equipements);
        }
    
        return redirect()->route('admin.finance.index')
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

            // Toutes les réservations de ce jour pour cet espace
            $reservations = Reservation::where('Id_Espace', $id)
                ->whereDate('date_debut', $date)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee'])
                ->with('abonnement') // pour accéder au type
                ->get(['date_debut','date_fin','Id_Abonnement']);

            $heuresOccupees = [];

            foreach ($reservations as $r) {
                $typeAbo = $r->abonnement->Type_Abonnement ?? null;

                // Si abonnement journalier ou mensuel → toutes les heures bloquées
                if ($typeAbo === 'journalier' || $typeAbo === 'mensuel') {
                    $heuresOccupees = $heuresPossibles;
                    break;
                }

                // Sinon réservation ponctuelle à l'heure
                $start = new \DateTime($r->date_debut);
                $end = new \DateTime($r->date_fin);

                for ($h = 9; $h < 17; $h++) {
                    $check = (clone $start)->setTime($h, 0);
                    if ($check >= $start && $check < $end) {
                        $heuresOccupees[] = sprintf('%02d:00', $h);
                    }
                }
            }

            // Heures libres
            $heuresDisponibles = array_values(array_diff($heuresPossibles, $heuresOccupees));

            return response()->json(['disponibles' => $heuresDisponibles]);
        }



    public function preview(Request $request)
        {
            // Validation
            $request->validate([
                'Id_Espace' => 'required|exists:espaces,Id_Espace',
                'email_client' => 'required|email|exists:utilisateurs,email',
                'date_debut' => 'required|date',
                'heure_debut' => 'required',
                'duree' => 'required|numeric|min:1',
                'duree_jour' => 'nullable|numeric|min:0',
                'duree_mois' => 'nullable|numeric|min:0',
                'equipements' => 'array'
            ]);

            // Récupération des données
            $utilisateur = Utilisateur::where('email', $request->email_client)->first();
            $espace = Espace::with('equipements')->findOrFail($request->Id_Espace);
            $abonnement = $request->Id_Abonnement ? Abonnement::find($request->Id_Abonnement) : null;
            
            // Calculs
            $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
            $total = 0;

            if ($abonnement) {
                if ($abonnement->Type_Abonnement === 'journalier') {
                    $duree = max(1, $request->duree_jour);
                    $fin = (clone $debut)->add(new \DateInterval('P'.$duree.'D'));
                    $total = $espace->tarif_journalier * $duree;
                } elseif ($abonnement->Type_Abonnement === 'mensuel') {
                    $duree = max(1, $request->duree_mois);
                    $fin = (clone $debut)->add(new \DateInterval('P'.$duree.'M'));
                    $total = $espace->tarif_mensuel * $duree;
                } else {
                    $duree = $request->duree;
                    $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));
                    $total = $espace->tarif_horaire * $duree;
                }
            } else {
                $duree = $request->duree;
                $fin = (clone $debut)->add(new \DateInterval('PT'.$duree.'H'));
                $total = $espace->tarif_horaire * $duree;
            }

            // Équipements
            $equipementsSelectionnes = [];
            $prixEquipements = 0;
            if($request->has('equipements')){
                foreach($request->equipements as $idEquip){
                    $equipement = Equipement::find($idEquip);
                    if($equipement) {
                        $prixEquipements += $equipement->prix;
                        $equipementsSelectionnes[] = $equipement;
                    }
                }
            }
            $total += $prixEquipements;

            // Vérification disponibilité
            $disponible = !Reservation::where('Id_Espace', $espace->Id_Espace)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee'])
                ->where(function($q) use ($debut, $fin){
                    $q->where('date_debut','<',$fin)->where('date_fin','>',$debut);
                })->exists();

                return view('admin.reservations.preview', compact(
                    'utilisateur', 'espace', 'abonnement', 'debut', 'fin', 
                    'duree', 'total', 'equipementsSelectionnes', 'prixEquipements', 
                    'disponible', 'request'
                ));
        }    
}
