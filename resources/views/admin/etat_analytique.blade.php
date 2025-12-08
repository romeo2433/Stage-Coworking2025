@extends('admin.layout')

@section('title', 'État Analytique')

@section('content')
<div class="container">
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-auto">
                <input type="date" name="start_date" value="{{ $start_date }}" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" value="{{ $end_date }}" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm">Filtrer</button>
            </div>
        </div>
    </form>
    
    <h3 class="mb-3">État Analytique des Paiements</h3>
    <p>Période : {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>

    {{-- Analyse par espace --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Analyse par Espace</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr>
                        <th>Espace</th>
                        <th>Nombre de paiements</th>
                        <th>Total Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paiementsParEspace as $espace => $paiementsEspace)
                        <tr>
                            <td>{{ $espace }}</td>
                            <td>{{ $paiementsEspace->count() }}</td>
                            <td>{{ number_format($paiementsEspace->sum('montant_payer'), 0, ',', ' ') }} Ar</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Analyse par client --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Analyse par Client</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Nombre de paiements</th>
                        <th>Total Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paiementsParClient as $client => $paiementsClient)
                        <tr>
                            <td>{{ $client }}</td>
                            <td>{{ $paiementsClient->count() }}</td>
                            <td>{{ number_format($paiementsClient->sum('montant_payer'), 0, ',', ' ') }} Ar</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
