<?php

namespace App\Http\Controllers;

use App\Models\Espace;
use Illuminate\Http\Request;

class EspaceController extends Controller
{
    public function showEquipements($id)
    {
        $espace = Espace::with(['equipements.type'])->findOrFail($id);
        return view('espaces.equipements', compact('espace'));
    }
}
