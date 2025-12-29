<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Utilisateur extends Authenticatable
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
    // Mutateur automatique : Laravel hashe le password à chaque création/modification
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'Id_Profil');
    }
    public function typeClient()
    {
        return $this->belongsTo(TypeClient::class, 'Id_Type_Client');
    }

}
