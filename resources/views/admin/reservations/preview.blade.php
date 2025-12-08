@extends('admin.layout')

@section('title', 'Prévisualisation Réservation')

@section('content')
<div class="container-fluid">

    {{-- Disponibilité --}}
    @if($disponible)
        <div class="alert alert-success">
            Disponible ! Il reste <strong>{{ $placesRestantes }}/{{ $placesTotales }}</strong> place(s) sur cet espace.
        </div>
    @else
        <div class="alert alert-danger">
            Indisponible — Toutes les {{ $placesTotales }} places sont déjà réservées pour ce créneau.
        </div>
    @endif

    <h2 class="text-center fw-bold text-primary mb-4">Prévisualisation de la Réservation</h2>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Récapitulatif</h5>
                </div>
                <div class="card-body">

                    {{-- Client --}}
                    <p><strong>Client :</strong> {{ $utilisateur->Prenom }} {{ $utilisateur->Nom }} | <small>{{ $utilisateur->email }}</small></p>

                    {{-- Espace --}}
                    <p><strong>Espace :</strong> {{ $espace->Nom }} (Capacité : {{ $espace->quantite }} place(s))</p>

                    {{-- Période --}}
                    <p><strong>Période :</strong> {{ $debut->format('d/m/Y H:i') }} → {{ $fin->format('d/m/Y H:i') }} | Durée : {{ $duree }}
                        @if($abonnement)
                            @if($abonnement->Type_Abonnement === 'journalier') jour(s)
                            @elseif($abonnement->Type_Abonnement === 'mensuel') mois
                            @else heure(s)
                            @endif
                        @else
                            heure(s)
                        @endif
                    </p>

                    {{-- Quantité --}}
                    <p><strong>Quantité réservée :</strong> {{ $request->quantite_reservation }} place(s)</p>

                    {{-- Équipements --}}
                    @if(count($equipementsSelectionnes) > 0)
                        <p><strong>Équipements :</strong></p>
                        <ul>
                            @foreach($equipementsSelectionnes as $equip)
                                <li>{{ $equip->nom }} - {{ number_format($equip->prix, 0, ',', ' ') }} Ar</li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Total --}}
                    <p class="text-end fw-bold text-primary">Total : {{ number_format($total, 0, ',', ' ') }} Ar</p>

                </div>

                {{-- Actions --}}
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary w-100">
                                 Annuler
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.reservations.store') }}" method="POST">
                                @csrf
                                {{-- Champs cachés --}}
                                <input type="hidden" name="Id_Espace" value="{{ $request->Id_Espace }}">
                                <input type="hidden" name="email_client" value="{{ $request->email_client }}">
                                <input type="hidden" name="quantite_reservation" value="{{ $request->quantite_reservation }}">
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
                                    Confirmer la réservation
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
