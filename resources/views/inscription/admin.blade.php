
<div class="container py-5">
    <h2 class="text-center mb-4 text-primary">Création d’un compte administrateur</h2>

    <form action="{{ route('admin.inscription.store') }}" method="POST" class="mx-auto" style="max-width: 500px;">
        @csrf

        <div class="mb-3">
            <label for="numero" class="form-label">Numéro</label>
            <input type="text" name="numero" class="form-control" required value="{{ old('numero') }}">
        </div>

        <div class="mb-3">
            <label for="Prenom" class="form-label">Prénom</label>
            <input type="text" name="Prenom" class="form-control" required value="{{ old('Prenom') }}">
        </div>

        <div class="mb-3">
            <label for="Nom" class="form-label">Nom</label>
            <input type="text" name="Nom" class="form-control" required value="{{ old('Nom') }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Créer l’administrateur</button>
    </form>
</div>
