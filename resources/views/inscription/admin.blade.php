<div class="container py-4">

    <!-- ===== FORMULAIRE ===== -->
    <div class="card shadow-sm mb-4 mx-auto" style="max-width:520px">
        <div class="card-header text-center fw-bold text-primary">
            Création compte administrateur
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.inscription.store') }}">
                @csrf

                <input class="form-control mb-2" name="numero" placeholder="Numéro" required>
                <input class="form-control mb-2" name="Prenom" placeholder="Prénom" required>
                <input class="form-control mb-2" name="Nom" placeholder="Nom" required>
                <input class="form-control mb-2" name="email" placeholder="Email" required>

                <select name="Id_Profil" class="form-select mb-2" required>
                    <option value="">Profil</option>
                    <option value="1">Administrateur</option>
                    <option value="3">Employé</option>
                </select>

                <input type="password" class="form-control mb-2" name="password" placeholder="Mot de passe" required>
                <input type="password" class="form-control mb-3" name="password_confirmation" placeholder="Confirmation" required>

                <button class="btn btn-primary w-100">Créer</button>
            </form>
        </div>
    </div>

    <!-- ===== TABLEAU ===== -->
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Liste des administrateurs
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Numéro</th>
                        <th>Profil</th>
                        <th>Mot de passe</th>
                        <th>Action</th>
                    </tr>
                </thead>
            
                <tbody>
                    @foreach($admins as $a)
                    <tr data-id="{{ $a->Id_Utilisateur }}">
                        @foreach(['Prenom','Nom','email','numero'] as $f)
                        <td>
                            <input class="form-control form-control-sm edit" name="{{ $f }}" value="{{ $a->$f }}">
                        </td>
                        @endforeach
            
                        <!-- PROFIL -->
                        <td>
                            <select class="form-select form-select-sm edit" name="Id_Profil">
                                <option value="1" {{ $a->Id_Profil == 1 ? 'selected' : '' }}>Administrateur</option>
                                <option value="3" {{ $a->Id_Profil == 3 ? 'selected' : '' }}>Employé</option>
                            </select>
                        </td>
            
                        <!-- MOT DE PASSE -->
                        <td>
                            <input type="password" class="form-control form-control-sm edit" name="password" placeholder="Nouveau mot de passe">
                        </td>
            
                        <!-- ACTION -->
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btn-save">💾</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>
<script>
    document.addEventListener('click', e => {
        const row = e.target.closest('tr');
        if (!row) return;
    
        // MODE ÉDITION
        if (e.target.classList.contains('btn-edit')) {
            row.querySelectorAll('.view').forEach(v => v.classList.add('d-none'));
            row.querySelectorAll('.edit').forEach(i => i.classList.remove('d-none'));
            row.querySelector('.btn-edit').classList.add('d-none');
            row.querySelector('.btn-save').classList.remove('d-none');
        }
    
        // SAUVEGARDE
        if (e.target.classList.contains('btn-save')) {
            let data = {_method:'PUT', _token:'{{ csrf_token() }}'};
            row.querySelectorAll('.edit').forEach(i => data[i.name] = i.value);
    
            fetch(`/admin/inscription/${row.dataset.id}`, {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify(data)
            })
            .then(r=>r.json())
            .then(r=>{
                if(!r.success) return alert('Erreur');
    
                row.querySelectorAll('.view').forEach((v,i)=>{
                    v.textContent = row.querySelectorAll('.edit')[i].value;
                    v.classList.remove('d-none');
                });
                row.querySelectorAll('.edit').forEach(i=>i.classList.add('d-none'));
                row.querySelector('.btn-save').classList.add('d-none');
                row.querySelector('.btn-edit').classList.remove('d-none');
            });
        }
    });
    </script>
  
    