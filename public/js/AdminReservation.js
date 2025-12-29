document.addEventListener('DOMContentLoaded', function () {
    // === ÉLÉMENTS DOM ===
    const espaceSelect         = document.getElementById('Id_Espace');
    const dateInput            = document.getElementById('date_debut');
    const heureSelect          = document.getElementById('heure_debut');
    const dureeInput           = document.getElementById('duree');
    const abonnementSelect     = document.getElementById('Id_Abonnement');
    const equipementsContainer = document.getElementById('equipements-container');
    const totalInput           = document.getElementById('total');
    const totalHidden          = document.getElementById('total_hidden');

    // Champs durée selon abonnement
    const champHeures = document.getElementById('champ-duree-heures');
    const champJours  = document.getElementById('champ-duree-jours');
    const champMois   = document.getElementById('champ-duree-mois');

    // === VARIABLES GLOBALES ===
    let tarifHoraire     = 0;
    let tarifJournalier  = 0;
    let tarifMensuel     = 0;
    let equipements      = []; // tableau d'objets { checkbox, quantiteInput, prix }
    let quantiteMaxEspace = 1;

    // === GESTION DES CHAMPS DURÉE SELON ABONNEMENT ===
    function gererChampsAbonnement() {
        const selectedOption = abonnementSelect.options[abonnementSelect.selectedIndex];
        const type = selectedOption?.dataset.type || '';

        champHeures.style.display = 'none';
        champJours.style.display  = 'none';
        champMois.style.display   = 'none';
        heureSelect.disabled = false;
        heureSelect.required = true;

        if (type === 'journalier') {
            champJours.style.display = 'block';
            heureSelect.required = false;
            heureSelect.value = '';
        } else if (type === 'mensuel') {
            champMois.style.display = 'block';
            heureSelect.required = false;
            heureSelect.value = '';
        } else {
            champHeures.style.display = 'block';
        }

        calculerTotal();
    }

    // === CALCUL DU TOTAL (identique au controller PHP) ===
    function calculerTotal() {
        let total = 0;
        const quantite = parseInt(document.getElementById('quantite_reservation')?.value) || 1;

        // --- Prix de base de la réservation ---
        let prixBase = 0;
        const selectedOption = abonnementSelect.options[abonnementSelect.selectedIndex];
        const abonnementValue = selectedOption?.value || '';

        if (abonnementValue !== '') {
            const type = selectedOption?.dataset.type || '';
            if (type === 'journalier') {
                const jours = Math.max(1, parseInt(document.getElementById('duree_jour')?.value) || 1);
                prixBase = tarifJournalier * jours;
            } else if (type === 'mensuel') {
                const mois = Math.max(1, parseInt(document.getElementById('duree_mois')?.value) || 1);
                prixBase = tarifMensuel * mois;
            } else {
                const heures = parseFloat(dureeInput.value) || 1;
                prixBase = tarifHoraire * heures;
            }
        } else {
            const heures = parseFloat(dureeInput.value) || 1;
            prixBase = tarifHoraire * heures;
        }

        total += prixBase * quantite;

        // --- Équipements avec quantité individuelle ---
        equipements.forEach(e => {
            if (e.checkbox?.checked) {
                const prixUnitaire = parseFloat(e.prix || 0);
                const quantiteEq = parseInt(e.quantiteInput?.value) || 1;
                total += prixUnitaire * quantiteEq;
            }
        });

        // --- Affichage ---
        totalInput.value = total.toLocaleString('fr-FR') + ' Ar';
        totalHidden.value = Math.round(total);
    }

    // === CHARGEMENT DES ÉQUIPEMENTS + INPUT QUANTITÉ ===
    function chargerEquipements() {
        const id = espaceSelect.value;
        if (!id) {
            equipementsContainer.innerHTML = '<p class="text-muted">Sélectionnez un espace.</p>';
            tarifHoraire = tarifJournalier = tarifMensuel = 0;
            equipements = [];
            calculerTotal();
            return;
        }

        fetch(`/admin/espaces/${id}/details`)
            .then(r => r.json())
            .then(data => {
                tarifHoraire    = parseFloat(data.tarif_horaire || 0);
                tarifJournalier = parseFloat(data.tarif_journalier || 0);
                tarifMensuel    = parseFloat(data.tarif_mensuel || 0);
                quantiteMaxEspace = parseInt(data.quantite || 1);

                equipementsContainer.innerHTML = `
                    <p><strong>Tarif horaire :</strong> ${tarifHoraire.toLocaleString('fr-FR')} Ar</p>
                    <p><strong>Tarif journalier :</strong> ${tarifJournalier.toLocaleString('fr-FR')} Ar</p>
                    <p><strong>Tarif mensuel :</strong> ${tarifMensuel.toLocaleString('fr-FR')} Ar</p>
                    <hr>
                `;

                equipements = [];

                if (data.equipements?.length > 0) {
                    data.equipements.forEach(e => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'd-flex align-items-center justify-content-between mb-3 p-3 border rounded bg-light';

                        // Checkbox + nom
                        const leftDiv = document.createElement('div');
                        leftDiv.className = 'form-check';

                        const cb = document.createElement('input');
                        cb.type = 'checkbox';
                        cb.className = 'form-check-input equipement-checkbox';
                        cb.name = 'equipements[]';
                        cb.value = e.Id_Equipement;
                        cb.dataset.prix = e.prix;
                        cb.id = 'equip_' + e.Id_Equipement;

                        const label = document.createElement('label');
                        label.className = 'form-check-label fw-bold ms-2';
                        label.htmlFor = cb.id;
                        label.textContent = e.nom;

                        leftDiv.appendChild(cb);
                        leftDiv.appendChild(label);

                        // Prix + input quantité
                        const rightDiv = document.createElement('div');
                        rightDiv.className = 'd-flex align-items-center';

                        const prixSpan = document.createElement('span');
                        prixSpan.className = 'text-muted me-3';
                        prixSpan.textContent = '+ ' + parseInt(e.prix).toLocaleString('fr-FR') + ' Ar/unité';

                        const quantiteInput = document.createElement('input');
                        quantiteInput.type = 'number';
                        quantiteInput.className = 'form-control form-control-sm quantite-equipement';
                        quantiteInput.min = 1;
                        quantiteInput.value = 1;
                        quantiteInput.style.width = '70px';
                        quantiteInput.style.display = 'none';
                        // === CLÉ : le name permet l'envoi direct au serveur ===
                        quantiteInput.name = 'quantite_equipements[' + e.Id_Equipement + ']';

                        rightDiv.appendChild(prixSpan);
                        rightDiv.appendChild(quantiteInput);

                        wrapper.appendChild(leftDiv);
                        wrapper.appendChild(rightDiv);
                        equipementsContainer.appendChild(wrapper);

                        // Stockage pour calcul
                        equipements.push({
                            checkbox: cb,
                            quantiteInput: quantiteInput,
                            prix: e.prix
                        });

                        // Événements
                        cb.addEventListener('change', function () {
                            quantiteInput.style.display = this.checked ? 'block' : 'none';
                            if (!this.checked) quantiteInput.value = 1;
                            calculerTotal();
                        });

                        quantiteInput.addEventListener('input', calculerTotal);
                    });
                } else {
                    equipementsContainer.innerHTML += '<p class="text-muted">Aucun équipement disponible.</p>';
                }

                calculerTotal();
            })
            .catch(err => {
                console.error('Erreur chargement détails espace:', err);
                equipementsContainer.innerHTML = '<p class="text-danger">Erreur de chargement.</p>';
            });
    }

    // === CHARGEMENT DES HEURES DISPONIBLES ===
    function chargerHeuresDisponibles() {
        const id = espaceSelect.value;
        const date = dateInput.value;

        heureSelect.innerHTML = '<option value="">-- Choisir --</option>';
        heureSelect.disabled = false;

        if (!id || !date) return;

        fetch(`/admin/espaces/${id}/heures-disponibles?date=${date}`)
            .then(r => r.json())
            .then(data => {
                heureSelect.innerHTML = '<option value="">-- Choisir --</option>';

                if (data.disponibles && data.disponibles.length > 0) {
                    data.disponibles.forEach(h => heureSelect.add(new Option(h, h)));
                } else {
                    heureSelect.innerHTML = '<option value="">Aucune heure disponible</option>';
                    heureSelect.disabled = true;
                }
            })
            .catch(() => {
                heureSelect.innerHTML = '<option value="">Erreur chargement</option>';
                heureSelect.disabled = true;
            });
    }

    // === MISE À JOUR QUANTITÉ MAX PLACES ===
    const quantiteInput = document.getElementById('quantite_reservation');

    function mettreAJourQuantiteMax() {
        const espaceId = espaceSelect.value;
        const date = dateInput.value;
        const heure = heureSelect.value;
        const duree = parseInt(dureeInput.value) || 1;

        if (!espaceId || !date || !heure) {
            quantiteInput.max = quantiteMaxEspace;
            return;
        }

        fetch(`/admin/espaces/${espaceId}/places-restantes?date=${date}&heure=${heure}&duree=${duree}`)
            .then(r => {
                if (!r.ok) throw new Error('Erreur serveur');
                return r.json();
            })
            .then(data => {
                const max = Math.max(parseInt(data.restante || 0), 0);
                quantiteInput.max = max;
                if (parseInt(quantiteInput.value) > max) quantiteInput.value = max;
                quantiteInput.disabled = (max === 0);
            })
            .catch(err => {
                console.error('Erreur fetch places-restantes:', err);
                quantiteInput.max = quantiteMaxEspace;
            });
    }

    // === ÉVÉNEMENTS ===
    abonnementSelect.addEventListener('change', () => {
        gererChampsAbonnement();
        calculerTotal();
    });

    espaceSelect.addEventListener('change', () => {
        chargerEquipements();
        chargerHeuresDisponibles();
        mettreAJourQuantiteMax();
        gererChampsAbonnement();
    });

    dateInput.addEventListener('change', () => {
        heureSelect.innerHTML = '<option value="">Chargement...</option>';
        heureSelect.disabled = true;
        chargerHeuresDisponibles();
        mettreAJourQuantiteMax();
    });

    heureSelect.addEventListener('change', () => {
        mettreAJourQuantiteMax();
        calculerTotal();
    });

    dureeInput.addEventListener('input', calculerTotal);
    document.getElementById('duree_jour')?.addEventListener('input', calculerTotal);
    document.getElementById('duree_mois')?.addEventListener('input', calculerTotal);
    document.getElementById('quantite_reservation')?.addEventListener('input', calculerTotal);

    // === INIT ===
    gererChampsAbonnement();
    mettreAJourQuantiteMax();
});