<?php

namespace App\Http\Controllers\InscriptionLogin;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InscriptionController extends Controller
{
    // Affichage du formulaire
    public function create()
    {
        return view('inscription.create'); 
    }

    // Traitement du formulaire
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'numero' => 'required|string|max:20',
            'Prenom' => 'required|string|max:50',
            'Nom' => 'required|string|max:50',
            'Entreprise' => 'nullable|string|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => 'required|captcha'
        ],
        [
            'g-recaptcha-response.required' => 'Veuillez confirmer que vous n’êtes pas un robot.',
            'g-recaptcha-response.captcha' => 'La vérification reCAPTCHA a échoué, veuillez réessayer.'
        ]);

    // Création de l'utilisateur 
        $utilisateur = Utilisateur::create([
            'numero' => $validatedData['numero'],
            'Prenom' => $validatedData['Prenom'],
            'Nom' => $validatedData['Nom'],
            'Entreprise' => $validatedData['Entreprise'] ?? null,
            'email' => $validatedData['email'],
            'password' => $request->password,
            'date_inscription' => Carbon::now()->toDateString(),
            'Id_Profil' => 4, 
        ]);

        

    // Redirection avec message de succès
    return redirect()->route('connexion.create')
                     ->with('success', 'Inscription réussie ! Un SMS de confirmation a été envoyé.');

    }



    
    
    public function createAdmin()
    {
        return view('inscription.admin');
    }

    
    public function storeAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'numero' => 'required|string|max:20',
            'Prenom' => 'required|string|max:50',
            'Nom' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:utilisateurs,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Utilisateur::create([
            'numero' => $validatedData['numero'],
            'Prenom' => $validatedData['Prenom'],
            'Nom' => $validatedData['Nom'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
            'date_inscription' => Carbon::now(),
            'Id_Profil' => 1,
        ]);

        return redirect()->route('connexion.create')->with('success', 'Administrateur créé avec succès !');
    }
}
