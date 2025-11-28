document.addEventListener('DOMContentLoaded', function() {
    const espaceSelect = document.getElementById('Id_Espace');
    const equipementsContainer = document.getElementById('equipements-container');
    const dureeHeureInput = document.getElementById('duree');
    const dureeJourInput = document.getElementById('duree_jour');
    const dureeMoisInput = document.getElementById('duree_mois');
    const totalInput = document.getElementById('total');
    const totalHidden = document.getElementById('total_hidden'); 
    const dateInput = document.getElementById('date_debut');
    const heureSelect = document.getElementById('heure_debut');
    const abonnementSelect = document.getElementById('Id_Abonnement');

    let tarifHoraire = 0;
    let equipements = [];

    // Fonction de calcul du total
    function calculerTotal() {
        let total = 0;

        // Total équipements
        equipements.forEach(e => {
            if (e.checkbox.checked) {
                total += parseFloat(e.prix);
            }
        });

        // Vérifier si un abonnement est choisi
        const selectedAbo = abonnementSelect.selectedOptions[0];
        const typeAbo = selectedAbo?.dataset?.type || '';
        const tarifJournalier = parseFloat(selectedAbo?.dataset?.tarif_journalier || 0);
        const tarifMensuel = parseFloat(selectedAbo?.dataset?.tarif_mensuel || 0);

        if(typeAbo === 'journalier') {
            total += tarifJournalier * parseInt(dureeJourInput.value || 0);
        } else if(typeAbo === 'mensuel') {
            total += tarifMensuel * parseInt(dureeMoisInput.value || 0);
        } else {
            // Aucun abonnement → tarif horaire
            total += tarifHoraire * parseFloat(dureeHeureInput.value || 0);
        }

        // Affichage
        totalInput.value = total.toLocaleString('fr-FR') + ' Ar';
        if(totalHidden) totalHidden.value = Math.round(total);
    }

    // Charger les équipements et tarif horaire selon l’espace
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
                tarifHoraire = parseFloat(data.tarif_horaire || 0);

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

    // Charger les heures disponibles
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
                    opt.selected = true;
                    opt.textContent = "Aucune heure disponible";
                    heureSelect.appendChild(opt);
                    heureSelect.disabled = true;
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

    function verifierHeureDispo() {
        const selectedAbo = abonnementSelect.selectedOptions[0];
        const typeAbo = selectedAbo?.dataset?.type || '';
    
        if (typeAbo === 'journalier' || typeAbo === 'mensuel') {
            heureSelect.value = '';
            heureSelect.disabled = true;
        } else {
            heureSelect.disabled = false;
            chargerHeuresDisponibles();
        }
    }
    
    // Appeler cette fonction à chaque changement d'abonnement
    abonnementSelect.addEventListener('change', () => {
        calculerTotal();
        verifierHeureDispo();
    });
    

    // Événements
    espaceSelect.addEventListener('change', function() {
        chargerEquipements();
        chargerHeuresDisponibles();
    });

    dateInput.addEventListener('change', chargerHeuresDisponibles);
    dureeHeureInput.addEventListener('input', calculerTotal);
    dureeJourInput.addEventListener('input', calculerTotal);
    dureeMoisInput.addEventListener('input', calculerTotal);
    abonnementSelect.addEventListener('change', calculerTotal);

    // Initialisation
    chargerEquipements();
});
