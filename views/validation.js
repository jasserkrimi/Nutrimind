// views/validation.js

document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");
    
    forms.forEach(form => {
        form.addEventListener("submit", function(event) {
            let isValid = true;
            let errorMsg = "La validation JavaScript a échoué :\n\n";

            // 1. Validation du Nom (Requise pour les 2 formulaires)
            const nom = form.querySelector("#nom");
            if (nom) {
                if (nom.value.trim() === "") {
                    isValid = false;
                    errorMsg += "⚠️ Le champ 'Nom' est obligatoire.\n";
                    nom.style.borderColor = "var(--danger)";
                } else if (nom.value.trim().length < 3) {
                    isValid = false;
                    errorMsg += "⚠️ Le 'Nom' doit contenir au moins 3 caractères.\n";
                    nom.style.borderColor = "var(--danger)";
                } else {
                    nom.style.borderColor = "var(--glass-border2)";
                }
            }

            // 2. Validation de la Catégorie (Activité Sportive)
            const categorie = form.querySelector("#categorie");
            if (categorie) {
                if (categorie.value === "") {
                    isValid = false;
                    errorMsg += "⚠️ Vous devez sélectionner une 'Catégorie'.\n";
                    categorie.style.borderColor = "var(--danger)";
                } else {
                    categorie.style.borderColor = "var(--glass-border2)";
                }
            }

            // 3. Validation de l'Activité Parente (Exercice)
            const activiteId = form.querySelector("#activite_id");
            if (activiteId) {
                if (activiteId.value === "") {
                    isValid = false;
                    errorMsg += "⚠️ Veuillez associer cet exercice à un 'Programme / Activité'.\n";
                    activiteId.style.borderColor = "var(--danger)";
                } else {
                    activiteId.style.borderColor = "var(--glass-border2)";
                }
            }

            // 4. Validation optionnelle : URL de la vidéo (Si remplie, elle doit être valide)
            const urlVideo = form.querySelector("#url_video");
            if (urlVideo && urlVideo.value.trim() !== "") {
                // Regex basique pour détecter un lien http:// ou https://
                const urlPattern = /^https?:\/\/.+/i;
                if (!urlPattern.test(urlVideo.value.trim())) {
                    isValid = false;
                    errorMsg += "⚠️ Le lien YouTube n'est pas valide (il doit commencer par http:// ou https://).\n";
                    urlVideo.style.borderColor = "var(--danger)";
                } else {
                    urlVideo.style.borderColor = "var(--glass-border2)";
                }
            }

            // Conclusion de la validation
            if (!isValid) {
                // Annule l'envoi du formulaire en PHP !
                event.preventDefault(); 
                alert(errorMsg);
            }
        });

        // Supprimer la bordure rouge dès que l'utilisateur commence à taper / corriger
        const inputs = form.querySelectorAll("input, select, textarea");
        inputs.forEach(input => {
            input.addEventListener("input", function() {
                this.style.borderColor = "var(--glass-border2)";
            });
        });
    });
});
