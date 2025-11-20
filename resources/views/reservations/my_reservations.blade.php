@extends('layouts.app')

@section('title', 'Mes Réservations')

@section('content')
<link rel="stylesheet" href="{{ asset('css/reservation-animations.css') }}">

<div class="container">
    <h2>Mes Réservations</h2>
    @if($reservations->isEmpty())
        <p>Vous n'avez aucune réservation pour le moment.</p>
    @else
        <div class="row g-3">
            @foreach($reservations as $res)
                <div class="col-md-6 col-lg-4">
                    <div class="card reservation-card h-100 d-flex flex-column">
                        <div class="card-header bg-primary text-white">
                            Réf: {{ $res->reference }} - Statut: {{ ucfirst(str_replace('_',' ',$res->Statut_Reservation)) }}
                        </div>
                        <div class="card-body flex-grow-1">
                            <p><strong>Espace :</strong> {{ $res->espace->Nom ?? '-' }}</p>
                            <p><strong>Date de début :</strong> {{ \Carbon\Carbon::parse($res->date_debut)->format('d/m/Y H:i') }}</p>
                            <p><strong>Date de fin :</strong> {{ \Carbon\Carbon::parse($res->date_fin)->format('d/m/Y H:i') }}</p>
                            <p><strong>Durée :</strong> {{ $res->duree_heures }} heures</p>
                            <p><strong>Équipements :</strong>
                                @if($res->equipements->isEmpty())
                                    Aucun
                                @else
                                    <ul class="mb-0">
                                        @foreach($res->equipements as $equip)
                                            <li>{{ $equip->nom }} x {{ $equip->pivot->Nombre_Ajout }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </p>
                            <p><strong>Total :</strong> {{ number_format($res->total,0,',',' ') }} Ar</p>
                        </div>
                          {{-- Footer 
                        @if(in_array($res->Statut_Reservation, ['confirmee','confirmé','partiellement_payee']))
                            <div class="card-footer mt-auto">
                                <a href="{{ route('paiements.create', ['reservation' => $res->Id_Reservation]) }}" class="btn btn-warning w-100">
                                    <i class="bi bi-credit-card"></i> Payer maintenant
                                </a>
                            </div>
                        @endif--}}

                        {{-- Boutons d'action --}}
                        @if($res->Statut_Reservation !== 'terminee' && $res->Statut_Reservation !== 'annulee')
                            <form action="{{ route('reservations.annuler', $res->Id_Reservation) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger w-50" onclick="return confirm('Voulez-vous vraiment annuler cette réservation ?')">
                                    Annuler
                                </button>
                            </form>
                        @endif
                        @if($res->Statut_Reservation == 'annulee')
                            <form action="{{ route('reservations.deleteClient', $res->Id_Reservation) }}" method="POST" onsubmit="return confirm('Supprimer cette réservation ?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        @endif
                        @if($res->Statut_Reservation == 'terminee')
                            <a href="{{ route('reservations.exportPdf', $res->Id_Reservation) }}" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Exporter en PDF
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script src="{{ asset('js/reservation-animations.js') }}"></script>
@endsection

