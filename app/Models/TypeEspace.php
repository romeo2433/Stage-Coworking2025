<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeEspace extends Model
{
    use HasFactory;

    protected $table = 'type_espaces';
    protected $primaryKey = 'Id_Type';
    protected $fillable = ['Type_Espace'];

    public function espaces()
    {
        return $this->hasMany(Espace::class, 'Id_Type', 'Id_Type');
    }
}
