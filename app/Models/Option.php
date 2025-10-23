<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'options';
    protected $primaryKey = 'Id_Option';
    protected $fillable = ['Nom'];
    public $timestamps = true;

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'Id_Option', 'Id_Option');
    }
}
