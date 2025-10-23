@extends('layouts.app')

@section('title', 'Mes Réservations')

@section('content')
<div class="container">
    <h2>Mes Réservations</h2>
    

    @if($reservations->isEmpty())
        <p>Vous n'avez aucune réservation pour le moment.</p>
    @else
        <div class="row">
            @foreach($reservations as $res)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Réf: {{ $res->reference }} - Statut: {{ ucfirst(str_replace('_',' ',$res->Statut_Reservation)) }}
                        </div>
                        <div class="card-body">
                            <p><strong>Espace :</strong> {{ $res->espace->Nom ?? '-' }}</p>
                            <p><strong>Date de début :</strong> {{ \Carbon\Carbon::parse($res->date_debut)->format('d/m/Y H:i') }}</p>
                            <p><strong>Date de fin :</strong> {{ \Carbon\Carbon::parse($res->date_fin)->format('d/m/Y H:i') }}</p>
                            <p><strong>Durée :</strong> {{ $res->duree_heures }} heures</p>
                            <p><strong>Équipements :</strong>
                                @if($res->equipements->isEmpty())
                                    Aucun
                                @else
                                    <ul>
                                        @foreach($res->equipements as $equip)
                                            <li>{{ $equip->nom }} x {{ $equip->pivot->Nombre_Ajout }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </p>
                            <p><strong>Total :</strong> {{ number_format($res->total,0,',',' ') }} Ar</p>
                            
                                    {{--  Bouton Paiement si la réservation est confirmée --}}
                                    @if($res->Statut_Reservation === 'confirmee' || $res->Statut_Reservation === 'confirmé'  || 
                                    $res->Statut_Reservation === 'partiellement_payee')
                                    <div class="mt-3">
                                        <a href="{{ route('paiements.create', ['reservation' => $res->Id_Reservation]) }}" class="btn btn-warning">
                                            <i class="bi bi-credit-card"></i> Payer maintenant
                                        </a>                                        
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
