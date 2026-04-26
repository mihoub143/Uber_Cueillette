var form = document.getElementById('offreForm');
var errorDiv = document.getElementById('error');

form.addEventListener('submit', function(e) {
    e.preventDefault(); // Bloque l'envoi

    var fruit = document.getElementById('fruit').value;
    var gouvernorat = document.getElementById('gouvernorat').value;
    var adresse = document.getElementById('adresse').value.trim();
    var dateDebut = document.getElementById('date_debut').value;
    var dateFin = document.getElementById('date_fin').value;
    var nbOuvriers = document.getElementById('nb_ouvriers').value;
    var prix = document.getElementById('prix').value;
    var dateLimite = document.getElementById('date_limite').value;


    if (new Date(dateFin) < new Date(dateDebut)) {
        showError("La date de fin doit être après la date de début.");
        return;
    }

    if (nbOuvriers <= 0) {
        showError("Le nombre d'ouvriers doit être supérieur à zéro.");
        return;
    }

    if (prix <= 0) {
        showError("Le prix par jour doit être supérieur à zéro.");
        return;
    }

    if (!dateLimite) {
        showError("Veuillez sélectionner la date limite pour postuler.");
        return;
    }

    if (new Date(dateLimite) < new Date()) {
        showError("La date limite pour postuler ne peut pas être passée.");
        return;
    }

    errorDiv.style.display = 'none';
    alert("Offre publiée avec succès !");
    form.submit();
});

function showError(message) {
    errorDiv.style.display = 'block';
    errorDiv.textContent = message;
}