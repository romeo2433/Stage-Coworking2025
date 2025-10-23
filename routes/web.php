<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InscriptionLogin\InscriptionController;
use App\Http\Controllers\InscriptionLogin\ConnexionController;
use App\Http\Controllers\TypeEspaceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\AdminPaiementController;
use App\Http\Controllers\PaiementController;


// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Inscription
Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/admin/inscription', [InscriptionController::class, 'createAdmin'])->name('admin.inscription.create');
Route::post('/admin/inscription', [InscriptionController::class, 'storeAdmin'])->name('admin.inscription.store');

// Connexion
Route::get('/connexion', [ConnexionController::class, 'create'])->name('connexion.create');
Route::post('/connexion', [ConnexionController::class, 'store'])->name('connexion.store');


// Dashboards
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('admin'); 

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard')->middleware('check.utilisateur.connecte');

Route::post('/logout', [ConnexionController::class, 'logout'])->name('connexion.logout');
Route::get('/logout', [ConnexionController::class, 'logout'])->name('logout');


Route::post('/logout', [ConnexionController::class, 'logout'])->name('connexion.logout');

// Types et espaces
Route::get('/types-espaces', [TypeEspaceController::class, 'index'])->name('types_espaces.index');

// Réservations (protégées par session)
Route::middleware(['check.utilisateur.connecte'])->group(function () {
    Route::get('reservation/{Id_Espace}', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservation/preview', [ReservationController::class, 'preview'])->name('reservations.preview');
    Route::post('/reservation/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::get('/mes-reservations', [ReservationController::class, 'myReservations'])->name('reservations.my');
    Route::get('/espaces/{id}/disponibilites', [App\Http\Controllers\ReservationController::class, 'getDisponibilites'])
    ->name('espaces.disponibilites');
});

Route::get('/calendrier', [App\Http\Controllers\CalendrierController::class, 'index'])
    ->name('calendrier.index');




    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Dashboard admin
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    
        // Espaces
        Route::get('espaces', [\App\Http\Controllers\TypeEspaceController::class, 'adminIndex'])
             ->name('espaces.index');
    
        // Réservations
        Route::get('reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])
             ->name('reservations.index');
        Route::post('reservations/{reservation}/confirm', [\App\Http\Controllers\Admin\ReservationController::class, 'confirm'])
             ->name('reservations.confirm');
        Route::post('reservations/{reservation}/reject', [\App\Http\Controllers\Admin\ReservationController::class, 'reject'])
             ->name('reservations.reject');


        Route::get('/paiements', [AdminPaiementController::class, 'index'])->name('paiements.index');
        Route::post('/paiements/{id}/payer', [AdminPaiementController::class, 'payer'])->name('paiements.payer');
        Route::post('/paiements/{id}/annuler', [AdminPaiementController::class, 'annuler'])->name('paiements.annuler');
    });
    
    Route::get('/paiements/create/{reservation}', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');