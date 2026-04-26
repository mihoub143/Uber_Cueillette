var form = document.getElementById('profileForm');
var errorDiv = document.getElementById('error');

form.addEventListener('submit', function(e) {
    e.preventDefault(); 

    var nom = document.getElementById('nom').value.trim();
    var prenom = document.getElementById('prenom').value.trim();
    var email = document.getElementById('email').value.trim();
    var telephone = document.getElementById('telephone').value.trim();
    var pseudo = document.getElementById('pseudo').value.trim();


    var lettersRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;
    var phoneRegex = /^[0-9]{8,}$/; 
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!lettersRegex.test(nom)) {
        showError("Le nom doit contenir uniquement des lettres.");
        return;
    }

    if (!lettersRegex.test(prenom)) {
        showError("Le prénom doit contenir uniquement des lettres.");
        return;
    }

    if (!emailRegex.test(email)) {
        showError("Email invalide.");
        return;
    }

    if (!phoneRegex.test(telephone)) {
        showError("Le téléphone doit contenir uniquement des chiffres (8 chiffres minimum).");
        return;
    }

    if (pseudo.length === 0) {
        showError("Le pseudo ne peut pas être vide.");
        return;
    }

    errorDiv.style.display = 'none';
    alert("Profil modifié avec succès !");
    form.submit();
});

function showError(message) {
    errorDiv.style.display = 'block';
    errorDiv.textContent = message;
}