<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TypeEspace;
use App\Models\Equipement;

class EspaceController extends Controller
{
    public function showEquipements($id)
        {
            $espace = Espace::with(['equipements.type'])->findOrFail($id);
            return view('espaces.equipements', compact('espace'));
        }
        public function editPhotos()
        {
            $types = TypeEspace::with('espaces')->get();
            return view('admin.espaces.photos', compact('types'));
        }
        
    public function updatePhoto(Request $request, $id)
        {
            $utilisateur = session('utilisateur');
            if (!$utilisateur || $utilisateur->Id_Profil != 1) {
            abort(403, 'Accès refusé');
            }
                $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg|max:4096',]);
                $espace = Espace::findOrFail($id); 

            // Condition mamafa ilay sary teo aloha ra misy 
            if ($espace->photo && Storage::disk('public')->exists('espaces/' . $espace->photo)) {
                Storage::disk('public')->delete('espaces/' . $espace->photo);
            }

            // misauve an ilay sary vaovao 
                $photoName = $request->file('photo')->getClientOriginalName();
                $request->file('photo')->storeAs('espaces', $photoName, 'public');
                $espace->photo = $photoName;
                $espace->save();
                return back()->with('success', 'Photo mise à jour avec succès.');
        }
    public function show($id)
        {
            $espace = Espace::with(['type', 'equipements'])->findOrFail($id);
        
            return view('admin.espaces.details', compact('espace'));
        }
    public function destroy($id)
        {
            $utilisateur = session('utilisateur');
            if (!$utilisateur || $utilisateur->Id_Profil != 1) {
                abort(403, 'Accès refusé');
            }
        
            $espace = Espace::findOrFail($id);
        
            // Supprimer la photo si elle existe
            if ($espace->photo && Storage::disk('public')->exists('espaces/' . $espace->photo)) {
                Storage::disk('public')->delete('espaces/' . $espace->photo);
            }
        
            // Supprimer l’espace
            $espace->delete();
        
            return back()->with('success', 'Espace supprimé avec succès.');
        }
        
    public function edit($id)
        {
            $espace = Espace::with(['type', 'equipements'])->findOrFail($id);
            $types = TypeEspace::all(); 
            $equipements = Equipement::all(); 

            return view('admin.espaces.edit', compact('espace', 'types', 'equipements'));
        }

    public function update(Request $request, $id)
        {
            $utilisateur = session('utilisateur');
            if (!$utilisateur || $utilisateur->Id_Profil != 1) {
                abort(403, 'Accès refusé');
            }

            $request->validate([
                'Nom' => 'required|string|max:255',
                'Statut' => 'required|string',
                'capacite' => 'required|integer|min:1',
                'tarif_horaire' => 'required|numeric|min:0',
                'tarif_journalier' => 'required|numeric|min:0',
                'tarif_mensuel' => 'required|numeric|min:0',
                'Id_Type' => 'required|exists:type_espaces,Id_Type',
                'equipements' => 'array|nullable'
            ]);

            $espace = Espace::findOrFail($id);

            $espace->update([
                'Nom' => $request->Nom,
                'Statut' => $request->Statut,
                'capacite' => $request->capacite,
                'tarif_horaire' => $request->tarif_horaire,
                'tarif_journalier' => $request->tarif_journalier,
                'tarif_mensuel' => $request->tarif_mensuel,
                'Id_Type' => $request->Id_Type,
            ]);

            // Synchroniser les équipements si relation many-to-many
            if ($request->has('equipements')) {
                $espace->equipements()->sync($request->equipements);
            }

            return redirect()->route('admin.espaces.photos', $espace->Id_Espace)
                ->with('success', 'Espace modifié avec succès.');
        }

    public function create()
        {
            $types = TypeEspace::all();
            $equipements = Equipement::all();
            return view('admin.espaces.create', compact('types', 'equipements'));
        }

    public function store(Request $request)
        {
            $utilisateur = session('utilisateur');
            if (!$utilisateur || $utilisateur->Id_Profil != 1) {
                abort(403, 'Accès refusé');
            }

            $request->validate([
                'Nom' => 'required|string|max:255',
                'Statut' => 'required|string',
                'capacite' => 'required|integer|min:1',
                'tarif_horaire' => 'required|numeric|min:0',
                'tarif_journalier' => 'required|numeric|min:0',
                'tarif_mensuel' => 'required|numeric|min:0',
                'Id_Type' => 'required|exists:type_espaces,Id_Type',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'equipements' => 'array|nullable'
            ]);

            // Enregistrement du nouvel espace
            $espace = new Espace($request->only([
                'Nom', 'Statut', 'capacite', 
                'tarif_horaire', 'tarif_journalier', 
                'tarif_mensuel', 'Id_Type'
            ]));

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                $photoName = time() . '_' . $request->file('photo')->getClientOriginalName();
                $request->file('photo')->storeAs('espaces', $photoName, 'public');
                $espace->photo = $photoName;
            }

            $espace->save();

            // Associer les équipements sélectionnés
            if ($request->has('equipements')) {
                $espace->equipements()->attach($request->equipements);
            }

            return redirect()->route('admin.espaces.photos')
                ->with('success', 'Espace ajouté avec succès.');
    }

}
