<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'profils';
    protected $primaryKey = 'Id_Profil';
    protected $fillable = ['profil'];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'Id_Profil');
    }
}
