<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InscriptionLogin\InscriptionController;
use App\Http\Controllers\InscriptionLogin\ConnexionController;
use App\Http\Controllers\TypeEspaceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\AdminPaiementController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\EspaceController;
use App\Http\Controllers\Admin\ReservationAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EquipementController;
use App\Http\Controllers\Admin\StatistiquesController;
use App\Http\Controllers\Admin\PlanningController;
use App\Http\Controllers\Api\ReservationApiController;
use App\Http\Controllers\Admin\EtatAnalytiqueController;


// Routes publiques
Route::get('/', function () {
    return redirect()->route('connexion.create');
});

Route::get('/connexion', [ConnexionController::class, 'create'])->name('connexion.create');


// Inscription
Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/admin/inscription', [InscriptionController::class, 'createAdmin'])->name('admin.inscription.create');
Route::post('/admin/inscription', [InscriptionController::class, 'storeAdmin'])->name('admin.inscription.store');

// Connexion
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
    Route::get('/espaces/{id}/disponibilites', [ReservationController::class, 'getDisponibilites'])
        ->name('espaces.disponibilites');
});

   
Route::get('/reservations', [ReservationApiController::class, 'index'])->name('reservations.api');
    Route::get('/espaces-disponibles', [ReservationApiController::class, 'espacesDisponibles']);
    Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier.index');


    Route::get('/paiements/create/{reservation}', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

    Route::put('/reservations/{id}/annuler', [ReservationController::class, 'annuler'])->name('reservations.annuler');
    Route::post('/reservations/{id}/delete-client', [ReservationController::class, 'deleteClient'])->name('reservations.deleteClient');
    Route::get('/reservations/{id}/export-pdf', [ReservationController::class, 'exportPdf'])->name('reservations.exportPdf');




    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {

        // ==================== ROUTES ACCESSIBLES AUX ADMINS (1) ET EMPLOYERS (3) ====================
    
        // Dashboard admin
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');    
    
        // Espaces (seulement lecture pour les employers)
        Route::get('espaces', [\App\Http\Controllers\TypeEspaceController::class, 'adminIndex'])
             ->name('espaces.index');
    
        // Réservations
        Route::get('reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations/{reservation}/confirm', [\App\Http\Controllers\Admin\ReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('reservations/{reservation}/reject', [\App\Http\Controllers\Admin\ReservationController::class, 'reject'])->name('reservations.reject');
        Route::get('espaces/{id}/heures-disponibles', [App\Http\Controllers\Admin\ReservationAdminController::class, 'heuresDisponibles']);
        Route::put('/reservations/{id}/update-duree', [\App\Http\Controllers\Admin\ReservationController::class, 'updateDuree'])->name('reservations.update_duree');
    
        // Paiements et finance
        Route::post('/reservations/{id}/payer', [AdminPaiementController::class, 'payer'])->name('reservations.payer');
        Route::get('finance', [AdminPaiementController::class, 'index'])->name('finance.index');
        Route::get('paiements', [AdminPaiementController::class, 'index'])->name('paiements.index');
        Route::get('paiements/pdf/{id}', [AdminPaiementController::class, 'exportOnePdf'])->name('paiements.pdf.one');
    
        // Création manuelle de réservation
        Route::get('/reservations/create', [ReservationAdminController::class, 'create'])->name('reservations.create');
        Route::post('/reservations/store', [ReservationAdminController::class, 'store'])->name('reservations.store');
        Route::post('/reservations/preview', [ReservationAdminController::class, 'preview'])->name('reservations.preview');
        Route::get('/espaces/{id}/details', [ReservationAdminController::class, 'espaceDetails']);
        Route::get('espaces/{id}/heures-disponibles', [ReservationAdminController::class, 'heuresDisponibles'])->name('espaces.heures-disponibles');
        Route::post('reservations/check-dispo', [ReservationAdminController::class, 'checkDispo']);
        Route::get('espaces/{id}/places-restantes', [ReservationAdminController::class, 'placesRestantes']);
    
        // Gestion utilisateurs
        Route::get('utilisateurs/create', [UserController::class, 'create'])->name('utilisateurs.create');
        Route::post('utilisateurs/store', [UserController::class, 'store'])->name('utilisateurs.store');
        Route::post('/utilisateurs/NouveauUtilisateur', [UserController::class, 'NouveauUtilisateur'])->name('utilisateurs.NouveauUtilisateur');
        Route::get('utilisateurs', [UserController::class, 'index'])->name('utilisateurs.show');
        Route::put('utilisateurs/{id}', [UserController::class, 'update'])->name('utilisateurs.update');
        Route::delete('utilisateurs/{id}', [UserController::class, 'destroy'])->name('utilisateurs.destroy');
    
        // Planning et checkin/checkout
        Route::post('planning/checkin/{reservation}', [PlanningController::class, 'checkin'])->name('planning.checkin');
        Route::post('planning/checkout/{reservation}', [PlanningController::class, 'checkout'])->name('planning.checkout');
        Route::get('planning/calendar', [PlanningController::class, 'calendar'])->name('planning.calendar');
        Route::post('reservations/{id}/checkin', [PlanningController::class, 'checkin'])->name('reservations.checkin');
        Route::post('reservations/{id}/checkout', [PlanningController::class, 'checkout'])->name('reservations.checkout');
    
        // ==================== ROUTES RÉSERVÉES UNIQUEMENT AU SUPER ADMIN (1) ====================
    
        Route::middleware('superadmin')->group(function () {
    
            // Upload et gestion photos des espaces
            Route::get('/espaces/photos', [EspaceController::class, 'editPhotos'])->name('espaces.photos');
            Route::put('/espaces/{id}/photo', [EspaceController::class, 'updatePhoto'])->name('espaces.updatePhoto');
    
            // CRUD complet des espaces
            Route::delete('/espaces/{id}', [EspaceController::class, 'destroy'])->name('espaces.destroy');
            Route::get('/espaces/{id}/edit', [EspaceController::class, 'edit'])->name('espaces.edit');
            Route::get('espaces/create', [EspaceController::class, 'create'])->name('espaces.create');
            Route::post('espaces/store', [EspaceController::class, 'store'])->name('espaces.store');
            Route::put('espaces/{id}', [EspaceController::class, 'update'])->name('espaces.update');
    
            // Équipements
            Route::get('/equipements', [EquipementController::class, 'index'])->name('equipements.index');
            Route::post('/equipements', [EquipementController::class, 'store'])->name('equipements.store');
            Route::put('equipements/{id}', [EquipementController::class, 'update'])->name('equipements.update');
            Route::delete('equipements/{id}', [EquipementController::class, 'destroy'])->name('equipements.destroy');
    
            // Types d'espaces
            Route::get('types-espaces', [TypeEspaceController::class, 'indexe'])->name('types.indexe');
            Route::post('types-espaces', [TypeEspaceController::class, 'store'])->name('typesespaces.store');
            Route::post('types/{id}/update', [TypeEspaceController::class, 'update'])->name('types.update');
    
            // Types d’équipements
            Route::post('equipements/type', [EquipementController::class, 'storeType'])->name('typesequipements.store');
            Route::put('equipements/type/{id}', [EquipementController::class, 'updateType'])->name('typesequipements.update');
            Route::delete('equipements/type/{id}', [EquipementController::class, 'destroyType'])->name('typesequipements.destroy');
    
            // Statistiques
            Route::get('statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
    
            // État analytique
            Route::get('/etat-analytique', [EtatAnalytiqueController::class, 'index'])->name('etat_analytique');
            Route::get('/etat-analytique/pdf', [EtatAnalytiqueController::class, 'exportPdf'])->name('etat_analytique.pdf');
            Route::get('/etat-analytique/excel', [EtatAnalytiqueController::class, 'exportExcel'])->name('etat_analytique.excel');
        });
    
    });
   