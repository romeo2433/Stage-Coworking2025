<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    use HasFactory;

    protected $table = 'mode';
    protected $primaryKey = 'Id_Mode';

    protected $fillable = [
        'Type_Mode',
    ];

    /**
     * 🔗 Relation : un mode peut avoir plusieurs paiements
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'Id_Mode', 'Id_Mode');
    }
}
