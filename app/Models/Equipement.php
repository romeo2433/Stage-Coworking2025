<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;

    protected $table = 'equipements';
    protected $primaryKey = 'Id_Equipement';
    public $timestamps = true;

    protected $fillable = ['prix', 'nom', 'Etat', 'Id_Type'];

    public function typeEquipement()
    {
        return $this->belongsTo(TypeEquipement::class, 'Id_Type');
    }

    public function espaces()
    {
        return $this->belongsToMany(Espace::class, 'espace_equipements', 'Id_Equipement', 'Id_Espace')
                    ->withPivot('Nombre_Equipements');
    }
    public function type()
    {
        return $this->belongsTo(TypeEquipement::class, 'Id_Type');
    }
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'equipements_reservation', 'Id_Equipement', 'Id_Reservation')
                    ->withPivot('Nombre_Ajout');
    }

}
