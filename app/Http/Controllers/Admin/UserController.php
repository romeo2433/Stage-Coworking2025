<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur; 
use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Equipement;
use Carbon\Carbon;


class UserController extends Controller
{
    // Formulaire de création
    public function create()
    {
        return view('admin.utilisateurs.create');
    }

    // Stocker le nouvel utilisateur
    public function store(Request $request)
        {
            // Validation
            $request->validate([
                'Id_Espace' => 'required|exists:espaces,Id_Espace',
                'email_client' => 'required|email|exists:utilisateurs,email',
                'date_debut' => 'required|date',
                'heure_debut' => 'required',
                'duree' => 'required|numeric|min:1',
                'equipements' => 'array'
            ]);

            // Récupérer l'utilisateur existant
            $utilisateur = Utilisateur::where('email', $request->email_client)->firstOrFail();

            // Préparer la réservation
            $espace = Espace::findOrFail($request->Id_Espace);
            $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
            $fin = (clone $debut)->add(new \DateInterval('PT' . $request->duree . 'H'));

            // Vérifier si l'espace est déjà réservé sur ce créneau
            $existing = Reservation::where('Id_Espace', $espace->Id_Espace)
                ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee'])
                ->where(function ($query) use ($debut, $fin) {
                    $query->where('date_debut', '<', $fin)
                        ->where('date_fin', '>', $debut);
                })
                ->exists();

            if ($existing) {
                return back()->withErrors([
                    'date_debut' => 'Cet espace est déjà réservé sur ce créneau. Veuillez choisir un autre horaire.'
                ])->withInput();
            }

            // Calculer le total
            $total = $espace->tarif_horaire * $request->duree;
            if ($request->has('equipements')) {
                foreach ($request->equipements as $idEquip) {
                    $equipement = Equipement::find($idEquip);
                    if ($equipement) {
                        $total += $equipement->prix;
                    }
                }
            }

            // Créer la réservation
            $reservation = new Reservation();
            $reservation->Id_Utilisateur = $utilisateur->Id_Utilisateur;
            $reservation->Id_Espace = $espace->Id_Espace;
            $reservation->date_debut = $debut;
            $reservation->date_fin = $fin;
            $reservation->duree = $request->duree;
            $reservation->montant_total = $total;
            $reservation->Statut_Reservation = 'confirmee';
            $reservation->save();

            // Attacher les équipements
            if ($request->has('equipements')) {
                $reservation->equipements()->sync($request->equipements);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Réservation créée avec succès.');
        }

        public function NouveauUtilisateur(Request $request)
        {
            $request->validate([
                'Prenom' => 'required|string|max:255',
                'Nom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email',
                'numero' => 'required|string|max:20|unique:utilisateurs,numero',
                'Entreprise' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);            
    
            $user = new Utilisateur();
            $user->Prenom = $request->Prenom;
            $user->Nom = $request->Nom;
            $user->email = $request->email;
            $user->numero = $request->numero;
            $user->Entreprise = $request->Entreprise;
            $user->password =$request->password;
            $user->date_inscription = Carbon::now()->toDateString();
            $user->Id_Profil = 4;
            $user->save();
    
            return redirect('admin/reservations/create')
            ->with('success', 'Utilisateur créé avec succès !');
        }
}
