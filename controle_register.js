document.querySelector("#registerForm").addEventListener("submit", function(e){

    let nom = document.getElementById("nom").value;
    let prenom = document.getElementById("prenom").value;
    let cin = document.getElementById("cin").value;
    let email = document.getElementById("email").value;
    let pseudo = document.getElementById("pseudo").value;
    let password = document.getElementById("password").value;

    let error = document.getElementById("error");

    error.textContent = "";
    error.style.display = "none";

    let nomRegex = /^[A-Za-z]+$/;
    let cinRegex = /^[0-9]{8}$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{6,}$/;


    if(!nomRegex.test(nom)||nom.length<3){
        error.textContent="Le nom doit contenir uniquement des lettres et au minimum 3 lettres";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    if(!nomRegex.test(prenom)||prenom.length<3){
        error.textContent="Le prénom doit contenir uniquement des lettres  et au minimum 3 lettres";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    if(!cinRegex.test(cin)){
        error.textContent="Le CIN doit contenir exactement 8 chiffres";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    if(!emailRegex.test(email)){
        error.textContent="Email invalide";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    if(pseudo.length < 3){
        error.textContent="Le pseudo doit contenir au moins 3 caractères";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    if(!passwordRegex.test(password)){
        error.textContent="Le mot de passe doit contenir majuscule, minuscule, chiffre et caractère spécial";
        error.style.display = "block";
        e.preventDefault();
        return;
    }

    error.style.display = "none";

});