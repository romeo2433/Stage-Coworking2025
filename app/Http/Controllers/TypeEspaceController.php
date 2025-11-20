<?php

namespace App\Http\Controllers;

use App\Models\TypeEspace;
use App\Models\Espace;
use Illuminate\Http\Request;

class TypeEspaceController extends Controller
{
    public function index()
    {
        $types = TypeEspace::with('espaces.equipements.type')->get();
        return view('types_espaces.index', compact('types'));
    }
    public function indexe()
    {
        $types = TypeEspace::all();
        return view('admin.types.index', compact('types'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'Type_Espace' => 'required|string|max:255|unique:type_espaces,Type_Espace',
        ]);

        TypeEspace::create([
            'Type_Espace' => $request->Type_Espace,
        ]);

        return back()->with('success', 'Type d’espace ajouté avec succès.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'Type_Espace' => 'required|string|max:255|unique:type_espaces,Type_Espace,' . $id . ',Id_Type',
        ]);

        $type = TypeEspace::findOrFail($id);
        $type->update(['Type_Espace' => $request->Type_Espace]);

        return back()->with('success', 'Type d’espace modifié avec succès.');
    }

}