@extends('admin.layout')

@section('title', 'Réservations en Espèces')

@section('content')

<div class="reservation-wrapper">
    <div class="card shadow-sm mb-5 p-4">
        {{-- 🔍 FORMULAIRE DE RECHERCHE --}}
        <form method="GET" action="{{ route('admin.reservations.montre') }}" class="row g-2 mb-4 align-items-end">
            <div class="col-md-4">
                <input type="text" name="reference" class="form-control" placeholder="Référence de réservation" value="{{ request('reference') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="nom_utilisateur" class="form-control" placeholder="Nom du client" value="{{ request('nom_utilisateur') }}">
            </div>
            <div class="col-md-4 d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="bi bi-search"></i> Rechercher
                </button>
                <a href="{{ route('admin.reservations.montre') }}" class="btn btn-secondary w-50">
                    <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                </a>
            </div>
        </form>

        <h2 class="mb-4 fw-bold text-primary">Réservations en Espèces</h2>

        @if($reservations->isEmpty())
            <div class="alert alert-info text-center">Aucune réservation trouvée.</div>
        @else
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Client</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Durée</th>
                    <th>Référence</th>
                    <th>Espace</th>
                    <th>Statut</th>
                    <th>Montant à payer</th>
                    <th>Référence Paiement</th>
                    <th>Date Paiement</th>
                    <th>Mode</th>
                    <th>Action</th>
                </tr>
            </thead>
        
            <tbody>
                @foreach($reservations as $res)
        
                    @php
                        $paiement = $res->paiement ?? ($res->paiements->first() ?? null);
                        $montant = $res->total - ($res->paiements->sum('montant_payer') ?? 0);
                    @endphp
        
                    <!-- Formulaire autour du TR -->
                    <form action="{{ route('admin.reservations.payer', $res->Id_Reservation) }}" method="POST">
                        @csrf
                        <tr class="text-center">
                            <td><input type="checkbox" class="reservation-checkbox" data-montant="{{ $montant }}"></td>
                            <td>{{ $res->utilisateur->Prenom ?? '' }} {{ $res->utilisateur->Nom ?? '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->Date_Debut)->format('d/m/Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->Date_Fin)->format('d/m/Y H:i') }}</td>
                            <td>{{ $res->duree_heures ?? '—' }} h</td>
                            <td>{{ $res->Reference ?? $res->reference ?? '—' }}</td>
                            <td>{{ $res->Espace->Nom ?? '—' }}</td>
                            <td>
                                @if($res->Statut_Reservation === 'confirmee')
                                    <span class="badge bg-success">Confirmée</span>
                                @elseif($res->Statut_Reservation === 'terminee')
                                    <span class="badge bg-secondary">Terminée</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($res->Statut_Reservation) }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($montant, 0, ',', ' ') }} Ar</td>
                            <!-- Référence Paiement -->
                            <td>
                                <input type="text" name="Reference" class="form-control form-control-sm" value="{{ $paiement->Reference ?? '' }}">
                            </td>
                            <!-- Date Paiement -->
                            <td>
                                <input type="datetime-local" name="date_paiement" class="form-control form-control-sm" 
                                    value="{{ $paiement && $paiement->date_paiement ? date('Y-m-d', strtotime($paiement->date_paiement)) : '' }}">
                            </td>
                            <!-- Mode Paiement -->
                            <td>
                                <select name="Id_Mode" class="form-select form-select-sm">
                                    @foreach($modes as $mode)
                                        <option value="{{ $mode->Id_Mode }}" {{ ($paiement->Id_Mode ?? '') == $mode->Id_Mode ? 'selected' : '' }}>
                                            {{ $mode->Type_Mode }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Bouton Action -->
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ $paiement ? 'Sauvegarder' : 'Créer & Sauvegarder' }}
                                </button>
                            </td>
                        </tr>
                    </form>        
                @endforeach
            </tbody>
        </table>        
            <div class="text-end mt-3">
                <h5>Total sélectionné : <span id="totalSelected" class="text-success fw-bold">0</span> Ar</h5>
            </div>
        @endif
    </div>
</div>
<script src="{{ asset('js/PrixAngro.js') }}"></script>
@endsection

