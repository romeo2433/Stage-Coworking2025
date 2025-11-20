document.addEventListener('DOMContentLoaded', function() {
    const espaceSelect = document.getElementById('Id_Espace');
    const equipementsContainer = document.getElementById('equipements-container');
    const dureeInput = document.getElementById('duree');
    const totalInput = document.getElementById('total');
    const totalHidden = document.getElementById('total_hidden'); 
    const dateInput = document.getElementById('date_debut');
    const heureSelect = document.getElementById('heure_debut');

    let tarifHoraire = 0;
    let equipements = [];

    // 🔹 Calcul du total (espace + équipements)
    function calculerTotal() {
        const duree = parseFloat(dureeInput.value) || 0;
        let total = tarifHoraire * duree;

        equipements.forEach(e => {
            if (e.checkbox.checked) {
                total += parseFloat(e.prix);
            }
        });

        totalInput.value = total.toLocaleString('fr-FR') + ' Ar';
        if (totalHidden) {
            totalHidden.value = Math.round(total);
        }
    }

    // 🔹 Charger les équipements et tarif horaire selon l’espace
    function chargerEquipements() {
        const espaceId = espaceSelect.value;

        if (!espaceId) {
            equipementsContainer.innerHTML = '<p class="text-muted">Sélectionnez un espace pour voir les équipements.</p>';
            tarifHoraire = 0;
            equipements = [];
            calculerTotal();
            return;
        }

        fetch(`/admin/espaces/${espaceId}/details`)
            .then(res => res.json())
            .then(data => {
                tarifHoraire = parseFloat(data.tarif_horaire) || 0;

                equipementsContainer.innerHTML = `
                    <p><strong>Tarif horaire :</strong> ${tarifHoraire.toLocaleString('fr-FR')} Ar</p>
                    <hr>
                `;

                equipements = [];

                if (data.equipements && data.equipements.length > 0) {
                    data.equipements.forEach(e => {
                        const div = document.createElement('div');
                        div.classList.add('form-check', 'mb-2');

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.className = 'form-check-input equipement-checkbox';
                        checkbox.value = e.Id_Equipement;
                        checkbox.dataset.prix = e.prix;
                        checkbox.id = `equipement-${e.Id_Equipement}`;
                        checkbox.name = 'equipements[]';

                        const label = document.createElement('label');
                        label.className = 'form-check-label ms-2';
                        label.htmlFor = checkbox.id;
                        label.textContent = `${e.nom} (${parseInt(e.prix).toLocaleString('fr-FR')} Ar)`;

                        div.appendChild(checkbox);
                        div.appendChild(label);
                        equipementsContainer.appendChild(div);

                        equipements.push({ checkbox: checkbox, prix: e.prix });

                        checkbox.addEventListener('change', calculerTotal);
                    });
                } else {
                    equipementsContainer.innerHTML += '<p class="text-muted">Aucun équipement disponible pour cet espace.</p>';
                }

                calculerTotal();
            })
            .catch(err => {
                console.error('Erreur lors du chargement des équipements:', err);
                equipementsContainer.innerHTML = '<p class="text-danger">Impossible de charger les équipements.</p>';
            });
    }

    // 🔹 Charger les heures disponibles
    function chargerHeuresDisponibles() {
        const espaceId = espaceSelect.value;
        const date = dateInput.value;
    
        heureSelect.disabled = false; // réactive le select par défaut
    
        if (!espaceId || !date) {
            heureSelect.innerHTML = '<option value="">-- Choisir une heure --</option>';
            return;
        }
    
        fetch(`/admin/espaces/${espaceId}/heures-disponibles?date=${date}`)
            .then(res => res.json())
            .then(data => {
                heureSelect.innerHTML = '<option value="">-- Choisir une heure --</option>';
    
                if (!data.disponibles || data.disponibles.length === 0) {
                    const opt = document.createElement('option');
                    opt.disabled = true;
                    opt.selected = true; // sélectionne l'option non disponible
                    opt.textContent = "Aucune heure disponible";
                    heureSelect.appendChild(opt);
                    heureSelect.disabled = true; // désactive le select entier
                    return;
                }
    
                data.disponibles.forEach(h => {
                    const opt = document.createElement('option');
                    opt.value = h;
                    opt.textContent = h;
                    heureSelect.appendChild(opt);
                });
            })
            .catch(err => {
                console.error('Erreur lors du chargement des heures:', err);
            });
    }
    

    // 🔄 Événements
    espaceSelect.addEventListener('change', function() {
        chargerEquipements();
        chargerHeuresDisponibles();
    });

    dateInput.addEventListener('change', chargerHeuresDisponibles);
    dureeInput.addEventListener('input', calculerTotal);
});
