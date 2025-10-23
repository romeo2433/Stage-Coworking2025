@extends('admin.layout')

@section('title', 'Détails Réservation')

@section('content')
    <h2>Détails de la réservation #{{ $reservation->Id_Reservation }}</h2>

    <p><strong>Client :</strong> {{ $reservation->utilisateur->Nom ?? 'N/A' }}</p>
    <p><strong>Espace :</strong> {{ $reservation->espace->Nom ?? 'N/A' }}</p>
    <p><strong>Statut :</strong> {{ $reservation->Statut_Reservation }}</p>

    @if ($paiement && $paiement->mode->Type_Mode === 'Espèces' && $paiement->Statut_Paiement == 'en attente')
        <form action="{{ route('admin.reservations.accepter', $reservation->Id_Reservation) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success"><i class="fas fa-check"></i> Accepter le paiement</button>
        </form>

        <form action="{{ route('admin.reservations.refuser', $reservation->Id_Reservation) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-danger"><i class="fas fa-times"></i> Refuser</button>
        </form>
    @endif
@endsection
