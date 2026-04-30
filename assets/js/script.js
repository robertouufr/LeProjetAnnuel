
const modifierBtn = document.querySelector('#modifierBtn');
const modal = document.querySelector('#modalRevenus');
const cancelBtn = document.querySelector('#cancelBtn');
const revenus = document.querySelector('#revenuInput');
const valider = document.querySelector('#confirmBtn');
const revenuDisplay = document.querySelector('#revenu-display');
const modifierLimitBtn = document.querySelector(".limit_modif");
const modifierFenetre = document.querySelector(".modifierLimitFenetre");
const cancelLimitBtn = document.querySelector("#cancelBtn_limit");
const montantLimite = document.querySelector("#montant-limit");
const moisLimite = document.querySelector("#Mois");
const catselect = document.querySelector(".cat-select_limits");
const confirmLimit = document.querySelector("#confirmBtn_limit");



modifierBtn.addEventListener('click', () => {
    modal.style.display = 'flex';
});

cancelBtn.addEventListener('click', () => {
    modal.style.display =  'none';
});

valider.addEventListener('click', () => {
    const valRevenu = parseFloat(revenus.value);

    if (valRevenu < 0 || isNaN(valRevenu) ) {
        alert("Entrez un nombre valide svp !");
        return;
    }

    else {
        const formData = new URLSearchParams();
        formData.append('revenu', valRevenu);

        fetch('code.php', {
            method : 'POST',
            body : formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    revenuDisplay.textContent = data.revnueValue + " €";
                    modal.style.display = 'none';
                    revenus.value = '';
                }
                else {
                    alert("Erreur lors de la mise à jour");
                }
            })
            .catch (err => {
                console.error("Erreur fetch", err);
                alert("Problème de connexion au serveur");
            });
    }
});

modifierLimitBtn.addEventListener('click', () => {modifierFenetre.style.display = 'flex';});
cancelLimitBtn.addEventListener('click', () => {modifierFenetre.style.display = 'none';});

confirmLimit.addEventListener('click', () => {
    const catValue = catselect.value;
    const montantValue = Number(montantLimite.value);
    const moisValue = Number(moisLimite.value);

    if(montantValue === "" || moisValue === "" || isNaN(montantValue) || isNaN(moisValue) || moisValue > 12 || moisValue <= 0) {
        alert("Entrez des valeurs valables s'il vous plait !");
        return;
    }

    const dataLimit = new URLSearchParams();
    dataLimit.append("catID", catValue);
    dataLimit.append("montantLimit", montantValue);
    dataLimit.append("moisLimite", moisValue);

    fetch("code.php", {
        method: "POST",
        body : dataLimit
    })
        .then(respLimit => respLimit.json())
        .then(data => {
            if(data.status === "success") {
                modifierFenetre.style.display = 'none';

                const cleanCatValue = catValue.trim();
                const carteChoisi = document.querySelector(`#limit-${cleanCatValue}`);

                if(carteChoisi) {
                    const textLimit = carteChoisi.querySelector('.limite-val');
                    if(textLimit) {
                        textLimit.textContent = parseFloat(montantValue).toFixed(2) + ' €';
                    }

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
                            if(percentageLabel) percentageLabel.style.backgroundColor = '#ffddd8';
                        }
                        else if (percentage >= 80) {
                            progressFill.style.backgroundColor = '#f39c12';
                            if(percentageLabel) percentageLabel.style.backgroundColor = '#fff3d9';
                        }
                        else {
                            progressFill.style.backgroundColor = '#2ecc71';
                            if(percentageLabel) percentageLabel.style.backgroundColor = '#c7f6d1';
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
