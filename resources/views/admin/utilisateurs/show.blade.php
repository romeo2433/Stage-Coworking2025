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
            <tr data-id="{{ $utilisateur->Id_Utilisateur }}">
                <td>
                    <span class="view">{{ $utilisateur->Prenom }}</span>
                    <input type="text" class="form-control form-control-sm edit d-none"
                           name="Prenom" value="{{ $utilisateur->Prenom }}">
                </td>
                <td>
                    <span class="view">{{ $utilisateur->Nom }}</span>
                    <input type="text" class="form-control form-control-sm edit d-none"
                           name="Nom" value="{{ $utilisateur->Nom }}">
                </td>
                <td>
                    <span class="view">{{ $utilisateur->email }}</span>
                    <input type="email" class="form-control form-control-sm edit d-none"
                           name="email" value="{{ $utilisateur->email }}">
                </td>
                <td>
                    <span class="view">{{ $utilisateur->numero }}</span>
                    <input type="text" class="form-control form-control-sm edit d-none"
                           name="numero" value="{{ $utilisateur->numero }}">
                </td>
                <td>
                    <span class="view">{{ $utilisateur->Entreprise }}</span>
                    <input type="text" class="form-control form-control-sm edit d-none"
                           name="Entreprise" value="{{ $utilisateur->Entreprise }}">
                </td>
                <td>
                    <span class="view">{{ $utilisateur->typeClient->type ?? '' }}</span>
                    <select class="form-select form-select-sm edit d-none" name="Id_Type_Client">
                        @foreach($typeClients as $type)
                            <option value="{{ $type->Id_Type_Client }}"
                                {{ $utilisateur->Id_Type_Client == $type->Id_Type_Client ? 'selected' : '' }}>
                                {{ $type->type }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm btn-edit">Modifier</button>
                    <button class="btn btn-success btn-sm btn-save d-none">Valider</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary mt-3">
        Retour
    </a>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            row.querySelectorAll('.view').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.edit').forEach(el => el.classList.remove('d-none'));

            this.classList.add('d-none');
            row.querySelector('.btn-save').classList.remove('d-none');
        });
    });

    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const id = row.dataset.id;

            let data = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            };

            // Récupérer toutes les valeurs des inputs/select en édition
            row.querySelectorAll('.edit').forEach(input => {
                data[input.name] = input.value;
            });

            fetch(`/admin/utilisateurs/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // Mise à jour de l'affichage pour les champs texte simples
                    row.querySelectorAll('.view').forEach(span => {
                        const editField = span.nextElementSibling;

                        if (editField && editField.tagName === 'INPUT') {
                            span.textContent = editField.value;
                        }
                        // Cas spécial pour le select (type de client)
                        else if (editField && editField.tagName === 'SELECT') {
                            const selectedOption = editField.options[editField.selectedIndex];
                            span.textContent = selectedOption ? selectedOption.text : '';
                        }

                        span.classList.remove('d-none');
                    });

                    // Masquer les champs édition et réafficher les boutons
                    row.querySelectorAll('.edit').forEach(el => el.classList.add('d-none'));
                    row.querySelector('.btn-save').classList.add('d-none');
                    row.querySelector('.btn-edit').classList.remove('d-none');
                } else {
                    alert('Erreur lors de la sauvegarde');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erreur réseau');
            });
        });
    });
</script>

@endsection