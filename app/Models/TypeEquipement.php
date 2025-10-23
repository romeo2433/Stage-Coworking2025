<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeEquipement extends Model
{
    use HasFactory;

    protected $table = 'type_equipements';
    protected $primaryKey = 'Id_Type';
    public $timestamps = true;

    protected $fillable = ['Type'];

    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'Id_Type');
    }
}
