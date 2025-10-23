@extends('layouts.app')

@section('title', 'Prévisualisation Réservation')

@section('content')
<div class="container">
    <h2>Prévisualisation Réservation : {{ $espace->Nom }}</h2>

    <p><strong>Date/Heure de début :</strong> {{ $debut->format('Y-m-d H:i') }}</p>
    <p><strong>Date/Heure de fin :</strong> {{ $fin->format('Y-m-d H:i') }}</p>

    <p><strong>Durée :</strong> {{ $duree }} heure(s)</p>

    <p><strong>Équipements sélectionnés :</strong></p>
    @if(empty($equipementsSelected))
        <p>Aucun équipement</p>
    @else
        <ul>
            @foreach($equipementsSelected as $equip)
                <li>{{ $equip->nom }} - {{ number_format($equip->prix,0,',',' ') }} Ar</li>
            @endforeach
        </ul>
    @endif

    <p><strong>Total à payer :</strong> {{ number_format($total,0,',',' ') }} Ar</p>

    <form action="{{ route('reservations.confirm') }}" method="POST" style="display:inline-block">
        @csrf
        <button type="submit" class="btn btn-success">Confirmer</button>
    </form>

    <form action="{{ route('types_espaces.index') }}" method="GET" style="display:inline-block">
        <button type="submit" class="btn btn-danger">Annuler</button>
    </form>
</div>
<link rel="stylesheet" href="{{ asset('css/reservation.css') }}">
@endsection
