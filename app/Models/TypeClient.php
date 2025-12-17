<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeClient extends Model
{
    protected $table = 'type_clients';
    protected $primaryKey = 'Id_Type_Client';

    public $timestamps = false;

    protected $fillable = [
        'type'
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'Id_Type_Client');
    }
}
