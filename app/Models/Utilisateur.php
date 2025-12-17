<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utilisateur extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'utilisateurs';
    protected $primaryKey = 'Id_Utilisateur';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'numero', 'Prenom', 'Entreprise', 'email', 'Nom',
        'date_inscription', 'Id_Profil','password'
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'Id_Profil');
    }
    public function typeClient()
    {
        return $this->belongsTo(TypeClient::class, 'Id_Type_Client');
    }

}
