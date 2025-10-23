@extends('admin.layout')

@section('title', 'Historique des paiements')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">💳 Historique des paiements</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Client</th>
                <th>Réservation</th>
                <th>Mode</th>
                <th>Montant deja payer </th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paiements as $paiement)
                <tr>
                    <td>{{ $paiement->reservation?->utilisateur?->Prenom }} {{ $paiement->reservation?->utilisateur?->Nom }}</td>
                    <td>#{{ $paiement->reservation?->reference }}</td>
                    <td>{{ $paiement->mode?->Type_Mode ?? '—' }}</td>
                    <td>{{ number_format($paiement->montant_payer, 0, ',', ' ') }} Ar</td>
                    <td>
                        @if($paiement->statut_paiement == 'paye')
                            <span class="badge bg-success">Payé</span>
                        @elseif($paiement->statut_paiement == 'partiel')
                            <span class="badge bg-danger">partiel</span>
                        @else
                            <span class="badge bg-warning text-dark">En attente</span>
                        @endif
                    </td>
                    <td>{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '—' }}</td>
                    <td>
                        @if($paiement->mode?->Type_Mode === 'Espèces' && $paiement->statut_paiement === 'en_attente')
                            <form action="{{ route('admin.paiements.payer', ['id' => $paiement->Id_Paiement]) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Payer
                                </button>
                            </form>
                    
                            <form action="{{ route('admin.paiements.annuler', ['id' => $paiement->Id_Paiement]) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </form>
                        @else
                            <em>Aucune action</em>
                        @endif
                    </td>                    
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Aucun paiement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
