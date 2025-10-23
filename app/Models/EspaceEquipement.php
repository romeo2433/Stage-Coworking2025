<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspaceEquipement extends Model
{
    use HasFactory;

    protected $table = 'espace_equipements';
    public $timestamps = false;

    protected $fillable = ['Id_Espace', 'Id_Equipement', 'Nombre_Equipements'];

    public function espace()
    {
        return $this->belongsTo(Espace::class, 'Id_Espace');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'Id_Equipement');
    }
}
