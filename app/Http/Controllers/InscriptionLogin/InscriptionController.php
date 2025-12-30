<?php

namespace App\Http\Controllers\InscriptionLogin;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InscriptionController extends Controller
{
    // Affichage du formulaire
    public function create()
    {
        $code = rand(100000, 999999); 

        session(['captcha_code' => $code]);

        return view('inscription.create', compact('code'));
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
            'captcha' => 'required'
        ]);
    
        // Vérification du Captcha
        if ($request->captcha != session('captcha_code')) {
            return back()->withErrors(['captcha' => 'Code incorrect, veuillez recommencer.'])
                         ->withInput();
        }
    
        // Création user
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
    
        return redirect()->route('connexion.create')
                         ->with('success', 'Inscription réussie !');
    }
    
    //Mamorona admnin   
    public function createAdmin()
    {
        // Récupérer uniquement les admins (1 = Super admin, 3 = Admin)
        $admins = Utilisateur::whereIn('Id_Profil', [1, 3])
            ->orderBy('Id_Utilisateur', 'desc')
            ->get();

        return view('inscription.admin', compact('admins'));
    }
    //Authentification Admin
    public function storeAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'numero' => 'required|string|max:20',
            'Prenom' => 'required|string|max:50',
            'Nom' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:utilisateurs,email',
            'password' => 'required|string|min:6|confirmed',
    
            // SEULEMENT 1 ou 3
            'Id_Profil' => ['required', Rule::in([1, 3])],
        ]);
    
        Utilisateur::create([
            'numero' => $validatedData['numero'],
            'Prenom' => $validatedData['Prenom'],
            'Nom' => $validatedData['Nom'],
            'email' => $validatedData['email'],
            'password'=> $validatedData['password'],
            'date_inscription' => Carbon::now(),
            'Id_Profil' => $validatedData['Id_Profil'],
        ]);
    
        return redirect()
            ->route('connexion.create')
            ->with('success', 'Compte administrateur créé avec succès !');
    }
    public function updateAdminAjax(Request $request, $id)
    {
        $request->validate([
            'Prenom' => 'required|string|max:50',
            'Nom'    => 'required|string|max:50',
            'email'  => 'required|email|unique:utilisateurs,email,' . $id . ',Id_Utilisateur',
            'numero' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $admin = Utilisateur::whereIn('Id_Profil', [1, 3])
            ->findOrFail($id);

        $admin->Prenom = $request->Prenom;
        $admin->Nom    = $request->Nom;
        $admin->email  = $request->email;
        $admin->numero = $request->numero;

        // Changement de mot de passe (optionnel)
        if ($request->filled('password')) {
            $admin->password = $request->password;
        }

        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Administrateur mis à jour avec succès',
            'admin'   => $admin
        ]);
    }
}
