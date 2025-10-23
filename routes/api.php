<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReservationApiController;

Route::get('/reservations', [ReservationApiController::class, 'index'])->name('api.reservations');
Route::get('/espaces-disponibles', [ReservationApiController::class, 'espacesDisponibles']);
