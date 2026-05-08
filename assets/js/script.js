// --- VARIABLES REVENUS ---
const modifierBtn = document.querySelector('#modifierBtn');
const modal = document.querySelector('#modalRevenus');
const cancelBtn = document.querySelector('#cancelBtn');
const revenus = document.querySelector('#revenuInput');
const valider = document.querySelector('#confirmBtn');
const revenuDisplay = document.querySelector('#revenu-display');

// --- VARIABLES LIMITES ---
const modifierLimitBtn = document.querySelector(".limit_modif");
const modifierFenetre = document.querySelector(".modifierLimitFenetre");
const cancelLimitBtn = document.querySelector("#cancelBtn_limit");
const montantLimite = document.querySelector("#montant-limit");
const moisLimite = document.querySelector("#Mois");
const catselect = document.querySelector(".cat-select_limits");
const confirmLimit = document.querySelector("#confirmBtn_limit");

// --- VARIABLES OBJECTIFS ---
const addObj = document.querySelector(".objectifBtn");
const cancelObj = document.querySelector(".objCancel");
const objModal = document.querySelector(".addObjectifModal");
const confirmObj = document.querySelector(".objConfirm");
const titreEpargne = document.querySelector("#titreEpargne");
const ObjectifEpargne = document.querySelector("#ObjectifEpargne");
const DateEpargne = document.querySelector("#DateEpargne");
const titreEntree = document.querySelector(".defi-title_entree");
const monatantObj = document.querySelector(".montantObj");

// --- VARIABLES EPARGNE ---
const epagneBtn = document.querySelector(".epagneBtn");
const addEpargneModal = document.querySelector(".addEpargneModal");
const epargneConfirm = document.querySelector(".epargneConfirm");
const epargneCancel = document.querySelector(".epargneCancel");
const montantEpargne = document.querySelector("#montantEpargne");


// ------GESTION DES REVENUS-------

modifierBtn.addEventListener('click', () => {
    modal.style.display = 'flex';
});

cancelBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

valider.addEventListener('click', () => {
    const valRevenu = parseFloat(revenus.value);

    if (valRevenu < 0 || isNaN(valRevenu)) {
        alert("Entrez un nombre valide svp !");
        return;
    }

    const formData = new URLSearchParams();
    formData.append('revenu', valRevenu);

    fetch('code.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                revenuDisplay.textContent = data.revnueValue + " €";
                modal.style.display = 'none';
                revenus.value = '';
            } else {
                alert("Erreur lors de la mise à jour");
            }
        })
        .catch(err => console.error("Erreur fetch:", err));
});


// --------GESTION DES LIMITES-------

modifierLimitBtn.addEventListener('click', () => { modifierFenetre.style.display = 'flex'; });
cancelLimitBtn.addEventListener('click', () => { modifierFenetre.style.display = 'none'; });

confirmLimit.addEventListener('click', () => {
    const catValue = catselect.value;
    const montantValue = Number(montantLimite.value);
    const moisValue = Number(moisLimite.value);

    if (montantValue === "" || moisValue === "" || isNaN(montantValue) || isNaN(moisValue) || moisValue > 12 || moisValue <= 0) {
        alert("Entrez des valeurs valables s'il vous plait !");
        return;
    }

    const dataLimit = new URLSearchParams();
    dataLimit.append("catID", catValue);
    dataLimit.append("montantLimit", montantValue);
    dataLimit.append("moisLimite", moisValue);

    fetch("code.php", {
        method: "POST",
        body: dataLimit
    })
        .then(respLimit => respLimit.json())
        .then(data => {
            if (data.status === "success") {
                modifierFenetre.style.display = 'none';
                const cleanCatValue = catValue.trim();
                const carteChoisi = document.querySelector(`#limit-${cleanCatValue}`);

                if (carteChoisi) {
                    const textLimit = carteChoisi.querySelector('.limite-val');
                    if (textLimit) textLimit.textContent = parseFloat(montantValue).toFixed(2) + ' €';

                    const progressFill = carteChoisi.querySelector('.progress-fill');
                    const spent = parseFloat(carteChoisi.getAttribute('data-spent')) || 0;
                    const newLimit = parseFloat(montantValue);
                    const percentageLabel = carteChoisi.querySelector('.percentage');

                    if (progressFill && newLimit > 0) {
                        let percentage = (spent * 100) / newLimit;
                        if (percentage > 100) percentage = 100;
                        progressFill.style.width = percentage + '%';

                        if (percentage >= 100) {
                            progressFill.style.backgroundColor = '#e74c3c';
                            if (percentageLabel) percentageLabel.style.backgroundColor = '#ffddd8';
                        } else if (percentage >= 80) {
                            progressFill.style.backgroundColor = '#f39c12';
                            if (percentageLabel) percentageLabel.style.backgroundColor = '#fff3d9';
                        } else {
                            progressFill.style.backgroundColor = '#2ecc71';
                            if (percentageLabel) percentageLabel.style.backgroundColor = '#c7f6d1';
                        }
                    }
                    carteChoisi.setAttribute('data-limit', montantValue);
                }
                montantLimite.value = "";
                moisLimite.value = "";
            }
        })
        .catch(err => console.error("Erreur:", err));
});


//---------GESTION DES OBJECTIFS---------

addObj.addEventListener("click", () => { objModal.style.display = "flex"; });
cancelObj.addEventListener("click", () => { objModal.style.display = "none"; });

confirmObj.addEventListener("click", () => {
    const titre = titreEpargne.value;
    const objectif = ObjectifEpargne.value;
    const date = DateEpargne.value;

    if (!titre || !objectif || isNaN(objectif) || objectif < 0 || !date) {
        alert("Entrez des informations valides s'il vous plais !");
        return;
    }

    const dataObjectif = new URLSearchParams();
    dataObjectif.append("titre", titre);
    dataObjectif.append("objectif", objectif);
    dataObjectif.append("date", date);

    fetch("code.php", {
        method: "POST",
        body: dataObjectif,
    })
        .then(resp => resp.json())
        .then(data => {
            if (data.status === "success") {
                objModal.style.display = "none";
                titreEntree.textContent = titre;
                monatantObj.textContent = parseFloat(objectif).toFixed(2) + ' €';
            }
        });
});


//-------VERSEMENT EPARGNE---------

epagneBtn.addEventListener("click", () => { addEpargneModal.style.display = "flex"; });
epargneCancel.addEventListener("click", () => { addEpargneModal.style.display = "none"; });

epargneConfirm.addEventListener("click", () => {
    const montantEpargneValue = montantEpargne.value;

    if (!montantEpargneValue || isNaN(montantEpargneValue) || montantEpargneValue <= 0) {
        alert("entrez un montant valide svp !");
        return;
    }

    const dataEpargn = new URLSearchParams();
    dataEpargn.append("montantEpargne", montantEpargneValue);

    fetch("code.php", {
        method: "POST",
        body: dataEpargn
    })
        .then(resp => resp.json())
        .then(data => {
            if (data.status === "success") {
                addEpargneModal.style.display = "none";
                montantEpargne.value = "";

                const curentEconomMontant = document.querySelector(".economMontant h2");
                const cureentObjectif = document.querySelector(".montantObj");
                const progressDefi = document.querySelector(".progress-fill-defi");
                const encoreElement = document.querySelector(".economiseCard h2");

                let currentVal = parseFloat(curentEconomMontant.innerText.replace(/[^\d.-]/g, '')) || 0;
                let objectif = parseFloat(cureentObjectif.innerText.replace(/[^\d.-]/g, '')) || 0;
                let addedAmount = parseFloat(montantEpargneValue);

                let total = currentVal + addedAmount;
                let reste = objectif - total;

                curentEconomMontant.textContent = total.toFixed(2) + ' €';

                if (encoreElement) {
                    encoreElement.textContent = (reste > 0 ? reste : 0).toFixed(2) + ' €';
                }

                if (progressDefi && objectif > 0) {
                    let percentagDefi = (total * 100) / objectif;
                    if (percentagDefi > 100) percentagDefi = 100;
                    progressDefi.style.width = percentagDefi + "%";
                }

            } else {
                alert("Erreur: " + data.message);
            }
        })
        .catch(err => console.error("Error:", err));
});