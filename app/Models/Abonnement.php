<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $table = 'abonnements';
    protected $primaryKey = 'Id_Abonnement';
    protected $fillable = ['Nom_Abonnement','Status_Abonnement'];
    public $timestamps = true;

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'Id_Abonnement', 'Id_Abonnement');
    }
}
