@extends('admin.layout')

@section('title', 'Prévisualisation Réservation')

@section('content')
<div class="container-fluid">
    <h2 class="text-center fw-bold text-primary mb-4">Prévisualisation de la Réservation</h2>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Récapitulatif</h5>
                </div>
                <div class="card-body">
                    <!-- Détails de la réservation -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Client</h6>
                            <p>{{ $utilisateur->Prenom }} {{ $utilisateur->Nom }}</p>
                            <p class="text-muted">{{ $utilisateur->email }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="fw-bold">Espace</h6>
                            <p>{{ $espace->Nom }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Période</h6>
                            <p><strong>Début:</strong> {{ $debut->format('d/m/Y H:i') }}</p>
                            <p><strong>Fin:</strong> {{ $fin->format('d/m/Y H:i') }}</p>
                            <p><strong>Durée:</strong> {{ $duree }} 
                                @if($abonnement)
                                    @if($abonnement->Type_Abonnement === 'journalier') jour(s)
                                    @elseif($abonnement->Type_Abonnement === 'mensuel') mois
                                    @else heure(s)
                                    @endif
                                @else
                                    heure(s)
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Total -->
                    <hr>
                    <div class="text-end">
                        <h4 class="fw-bold text-primary">Total: {{ number_format($total, 0, ',', ' ') }} Ar</h4>
                    </div>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary w-100">
                                ← Modifier
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.reservations.store') }}" method="POST">
                                @csrf
                                <!-- Champs cachés -->
                                <input type="hidden" name="Id_Espace" value="{{ $request->Id_Espace }}">
                                <input type="hidden" name="email_client" value="{{ $request->email_client }}">
                                <input type="hidden" name="Id_Abonnement" value="{{ $request->Id_Abonnement }}">
                                <input type="hidden" name="date_debut" value="{{ $request->date_debut }}">
                                <input type="hidden" name="heure_debut" value="{{ $request->heure_debut }}">
                                <input type="hidden" name="duree" value="{{ $request->duree }}">
                                <input type="hidden" name="duree_jour" value="{{ $request->duree_jour }}">
                                <input type="hidden" name="duree_mois" value="{{ $request->duree_mois }}">
                                @if($request->has('equipements'))
                                    @foreach($request->equipements as $equipement)
                                        <input type="hidden" name="equipements[]" value="{{ $equipement }}">
                                    @endforeach
                                @endif
                                
                                <button type="submit" class="btn btn-success w-100">
                                    ✅ Confirmer la réservation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection