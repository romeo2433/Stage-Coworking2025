@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container">
<h2>Équipements de {{ $espace->Nom }}</h2>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Nom équipement</th>
            <th>Prix</th>
            <th>État</th>
            <th>Type</th>
            <th>Nombre</th>
        </tr>
    </thead>
    <tbody>
        @foreach($espace->equipements as $equipement)
        <tr>
            <td>{{ $equipement->nom }}</td>
            <td>{{ number_format($equipement->prix, 0, ',', ' ') }} Ar</td>
            <td>{{ $equipement->Etat }}</td>
            <td>{{ $equipement->type->Type }}</td>
            <td>{{ $equipement->pivot->Nombre_Equipements }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mb-3 text-center">
    <a href="{{ route('types_espaces.show', ['id' => $espace->Id_Type]) }}" class="btn btn-secondary">
        Retour au type d’espace
    </a>
</div>

</div>
@endsection