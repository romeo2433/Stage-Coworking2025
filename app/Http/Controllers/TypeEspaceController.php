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
}