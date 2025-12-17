@extends('admin.layout')

@section('content')
<div class="container mt-4">

    <h3 class="mb-3">Liste des utilisateurs</h3>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Prenom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Numéro</th>
                <th>Entreprise</th>
                <th>type de clients</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($utilisateurs as $utilisateur)
            <tr>

                <form action="{{ route('admin.utilisateurs.update', $utilisateur->Id_Utilisateur) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <td>
                        <input type="text" name="Prenom" class="form-control"
                               value="{{ $utilisateur->Prenom }}">
                    </td>

                    <td>
                        <input type="text" name="Nom" class="form-control"
                               value="{{ $utilisateur->Nom }}">
                    </td>

                    <td>
                        <input type="email" name="email" class="form-control"
                               value="{{ $utilisateur->email }}">
                    </td>

                    <td>
                        <input type="text" name="numero" class="form-control"
                               value="{{ $utilisateur->numero }}">
                    </td>

                    <td>
                        <input type="text" name="Entreprise" class="form-control"
                               value="{{ $utilisateur->Entreprise }}">
                    </td>
                    <td>
                        <select name="Id_Type_Client" class="form-select">
                            @foreach($typeClients as $type)
                                <option value="{{ $type->Id_Type_Client }}"
                                    {{ $utilisateur->Id_Type_Client == $type->Id_Type_Client ? 'selected' : '' }}>
                                    {{ $type->type }}
                                </option>
                            @endforeach
                        </select>
                    </td>                    

                    <td class="d-flex gap-2">

                        <!-- Bouton enregistrer -->
                        <button class="btn btn-success btn-sm" type="submit">
                            Enregistrer
                        </button>

                </form>

                        <!-- Formulaire supprimer -->
                        <form action="{{ route('admin.utilisateurs.destroy', $utilisateur->Id_Utilisateur) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer cet utilisateur ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                Supprimer
                            </button>
                        </form>

                    </td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary mt-3">
        Retour
    </a>
</div>
@endsection
