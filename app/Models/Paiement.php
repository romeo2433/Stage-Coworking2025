<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';
    protected $primaryKey = 'Id_Paiement';

    protected $fillable = [
        'Reference',
        'montant_payer',
        'montant_Impayer',
        'date_paiement',
        'statut_paiement',
        'Id_Reservation',
        'Id_Mode',
        'created_by',
    ];

    /**
     * 🔗 Relation : un paiement appartient à une réservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'Id_Reservation', 'Id_Reservation');
    }

    /**
     * 🔗 Relation : un paiement appartient à un mode de paiement
     */
    public function mode()
    {
        return $this->belongsTo(Mode::class, 'Id_Mode', 'Id_Mode');
    }
    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
