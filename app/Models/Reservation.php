<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $primaryKey = 'Id_Reservation';
    public $timestamps = true;

    protected $fillable = [
        'reference',
        'date_debut',
        'date_fin',
        'duree_heures',
        'total',
        'Statut_Reservation',
        'Id_Abonnement',
        'Id_Option',
        'Id_Type',
        'Id_Espace',
        'Id_Utilisateur'
    ];

    // Relations
    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class, 'Id_Abonnement', 'Id_Abonnement');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'Id_Option', 'Id_Option');
    }

    public function type()
    {
        return $this->belongsTo(TypeReservation::class, 'Id_Type', 'Id_Type');
    }

    public function espace()
    {
        return $this->belongsTo(Espace::class, 'Id_Espace', 'Id_Espace');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'Id_Utilisateur', 'Id_Utilisateur');
    }
    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'equipements_reservation', 'Id_Reservation', 'Id_Equipement')
                    ->withPivot('Nombre_Ajout');
    }
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'Id_Reservation', 'Id_Reservation');
    }

}
