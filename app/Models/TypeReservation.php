<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeReservation extends Model
{
    use HasFactory;

    protected $table = 'type_reservations';
    protected $primaryKey = 'Id_Type';
    protected $fillable = ['Type'];
    public $timestamps = true;

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'Id_Type', 'Id_Type');
    }
}
