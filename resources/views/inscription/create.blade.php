<!DOCTYPE html>
<html lang="fr">
    <head>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Connexion Client</title>
        
            <!-- Bootstrap CSS -->
            <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        
            <!-- Formulaires CSS -->
            <link href="{{ asset('assets/css/forms.css') }}" rel="stylesheet">
        </head>    
    </head>
    
    <body class="bg-light">

    <main class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card text-center">
            <img src="{{ asset('assets/img/ccia.png') }}" alt="Logo" style="width: 50px; margin-bottom:5px;">
            <h4>Créer un compte client</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('inscription.store') }}" method="POST" class="row g-1">
                @csrf
                <div class="col-12">
                    <label class="form-label">Numéro de téléphone</label>
                    <input type="text" name="numero" class="form-control" required>
                </div>
            
                <div class="col-12">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="Prenom" class="form-control" required>
                </div>
            
                <div class="col-12">
                    <label class="form-label">Nom</label>
                    <input type="text" name="Nom" class="form-control" required>
                </div>
            
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            
                <div class="col-12">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>                
            
                <div class="col-12">
                    <label class="form-label">Entreprise (optionnel)</label>
                    <input type="text" name="Entreprise" class="form-control">
                </div>
            
                <!-- ✅ reCAPTCHA Google -->
                <div class="col-12 my-3">
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                {!! NoCaptcha::renderJs() !!}
            
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </div>
            
                <div class="col-12 text-center">
                    <p>Déjà un compte ? <a href="{{ route('connexion.create') }}">Se connecter</a></p>
                </div>
            </form>
            
           
            
        </div>
    </main>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
