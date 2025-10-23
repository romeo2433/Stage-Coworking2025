<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espace extends Model
{
    use HasFactory;

    protected $table = 'espaces';
    protected $primaryKey = 'Id_Espace';
    protected $fillable = [
        'Nom', 'Statut', 'capacite', 'tarif_horaire', 
        'tarif_journalier', 'tarif_mensuel', 'Id_Type'
    ];

    public function type()
    {
        return $this->belongsTo(TypeEspace::class, 'Id_Type', 'Id_Type');
    }
    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'espace_equipements', 'Id_Espace', 'Id_Equipement')
                    ->withPivot('Nombre_Equipements');
    }
    public function typeEspace()
    {
        return $this->belongsTo(TypeEspace::class, 'Id_Type');
    }

}
