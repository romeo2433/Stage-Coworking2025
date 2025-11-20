@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container py-5 text-center">

    {{-- Logo ou avatar --}}
    <div class="mb-4">
        <img src="{{ asset('assets/img/ccia.png') }}" alt="Logo" class="rounded-circle shadow-sm" style="height:80px; width:80px;">
    </div>

    {{-- Titre principal --}}
    <h2 class="text-primary fw-bold mb-3">
        Bienvenue sur le tableau de bord administrateur
    </h2>   
</div>
@endsection
