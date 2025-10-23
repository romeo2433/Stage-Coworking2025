<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Model
{
    use HasFactory;
    protected $table = 'utilisateurs';
    protected $primaryKey = 'Id_Utilisateur';
    public $timestamps = false;
    protected $fillable = [
        'numero', 'Prenom', 'Entreprise', 'email', 'Nom',
        'date_inscription', 'Id_Profil','password'
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'Id_Profil');
    }
}
