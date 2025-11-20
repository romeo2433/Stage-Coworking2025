<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $table = 'checkins_';
    protected $primaryKey = 'Id_Checkin';
    public $timestamps = true;

    protected $fillable = ['heure_arrivee', 'heure_sortie', 'notes', 'Id_Reservation'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'Id_Reservation', 'Id_Reservation');
    }
}
