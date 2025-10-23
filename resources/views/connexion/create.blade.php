<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client</title>

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/forms.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card text-center p-3" style="width: 100%; max-width: 400px;">
        <h4 class="mb-3">Connexion Client</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('connexion.store') }}" method="POST" class="row g-2">
            @csrf
            <div class="col-12">
                <label class="form-label">Numéro de téléphone ou Email</label>
                <input type="text" placeholder="Email ou numéro" name="identifiant" class="form-control form-control-sm" required>
                @error('identifiant')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control form-control-sm" placeholder="Mot de passe" required>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary w-100 btn-sm">Se connecter</button>
            </div>
        </form>

        <p class="text-center mt-3 mb-0">
            Pas encore inscrit ? 
            <a href="{{ route('inscription.create') }}">Créer un compte</a>
        </p>
    </div>
</div>

<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>