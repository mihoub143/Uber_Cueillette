var form = document.getElementById('registerForm');
var errorDiv = document.getElementById('error');

form.addEventListener('submit', function(e) {
    e.preventDefault(); 

    var nom = document.getElementById('nom').value.trim();
    var prenom = document.getElementById('prenom').value.trim();
    var email = document.getElementById('email').value.trim();
    var cin = document.getElementById('cin').value.trim();
    var photo = document.getElementById('photo').files[0];
    var pseudo = document.getElementById('pseudo').value.trim();
    var password = document.getElementById('password').value.trim();

    var lettersRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;
    var cinRegex = /^[0-9]{8}$/;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var allowedPhotoTypes = ['image/jpeg', 'image/png', 'image/jpg'];

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
    if (!cinRegex.test(cin)) {
        showError("Le CIN doit contenir exactement 8 chiffres.");
        return;
    }
    if (!photo) {
        showError("Veuillez choisir une photo d'identité.");
        return;
    }
    if (allowedPhotoTypes.indexOf(photo.type) === -1) {
        showError("La photo doit être au format JPG ou PNG.");
        return;
    }
    if (pseudo.length === 0) {
        showError("Le pseudo ne peut pas être vide.");
        return;
    }
    if (password.length < 6) {
        showError("Le mot de passe doit contenir au moins 6 caractères.");
        return;
    }
    errorDiv.style.display = 'none';
    alert("Inscription réussie !");
    form.submit();
});

function showError(message) {
    errorDiv.style.display = 'block';
    errorDiv.textContent = message;
}