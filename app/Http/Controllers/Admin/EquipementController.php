<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipement;
use App\Models\TypeEquipement;

class EquipementController extends Controller
{
    public function index()
    {
        $types = TypeEquipement::orderBy('Type')->get();
        $typesEquipements = $types; // alias attendu par la vue
    
        $equipements = Equipement::with('type')->orderBy('nom')->get();
    
        return view('admin.equipements.index', compact('equipements', 'types', 'typesEquipements'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:equipements,nom',
            'prix' => 'required|numeric|min:0',
            'Id_Type' => 'required|exists:type_equipements,Id_Type',
        ]);        

        Equipement::create([
            'nom' => $request->nom,
            'prix' => $request->prix,
            'Etat' => 'OK',
            'Id_Type' => $request->Id_Type,
        ]);

        return back()->with('success', 'Équipement ajouté avec succès !');
    }
    public function update(Request $request, $id)
        {
            $request->validate([
                'nom' => 'required|string|max:255|unique:equipements,nom,' . $id . ',Id_Equipement',
                'prix' => 'required|numeric|min:0',
                //'Id_Type' => 'required|exists:type_equipements,Id_Type',
            ]);

            $equipement = Equipement::findOrFail($id);
            $equipement->update([
                'nom' => $request->nom,
                'prix' => $request->prix,
                //'Id_Type' => $request->Id_Type,
            ]);
            return back()->with('success', 'Équipement modifié avec succès !');
        }   
    public function destroy($id)
        {
            $equipement = Equipement::findOrFail($id);
            $equipement->delete();
        
            return back()->with('success', 'Équipement supprimé avec succès !');
        }

        public function storeType(Request $request)
            {
                $request->validate([
                    'Type' => 'required|string|max:50|unique:type_equipements,Type',
                ]);

                TypeEquipement::create([
                    'Type' => $request->Type,
                ]);

                return back()->with('success', 'Type d’équipement ajouté !');
            }
     
            public function updateType(Request $request, $id)
            {
                $request->validate([
                    'Type' => 'required|string|max:50|unique:type_equipements,Type,' . $id . ',Id_Type',
                ]);
            
                $type = TypeEquipement::findOrFail($id);
                $type->update([
                    'Type' => $request->Type
                ]);
            
                return back()->with('success', 'Type modifié avec succès !');
            }
            
            public function destroyType($id)
            {
                $type = TypeEquipement::findOrFail($id);
                $type->delete();
            
                return back()->with('success', 'Type supprimé avec succès !');
            }
            
        
        
}
