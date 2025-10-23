<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipementReservation extends Model
{
    protected $table = 'equipements_reservation';
    public $timestamps = false;

    protected $primaryKey = ['Id_Equipement', 'Id_Reservation'];
    public $incrementing = false;

    protected $fillable = [
        'Id_Equipement',
        'Id_Reservation',
        'Nombre_Ajout',
    ];

    // Relation vers Equipement
    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'Id_Equipement');
    }

    // Relation vers Reservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'Id_Reservation');
    }
}
