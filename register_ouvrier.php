<?php
require_once("config.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $pseudo = $_POST['pseudo'];
    $password = $_POST['password']; 
    $desc = $_POST['description'] ?? '';

    try {
        if (!empty($_FILES['photo']['tmp_name'])) {
            $file = $_FILES['photo'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $file_type = mime_content_type($file['tmp_name']);
            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (in_array($extension, $allowed_ext)) {
                $photoData = file_get_contents($file['tmp_name']);
                $sql = "INSERT INTO ouvrier (nom, prenom, CIN, email, pseudo, password, description, photo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(1, $nom);
                $stmt->bindParam(2, $prenom);
                $stmt->bindParam(3, $cin);
                $stmt->bindParam(4, $email);
                $stmt->bindParam(5, $pseudo);
                $stmt->bindParam(6, $password);
                $stmt->bindParam(7, $desc);
                $stmt->bindParam(8, $photoData, PDO::PARAM_LOB);
                
                if ($stmt->execute()) {
                    header("Location: login.php?success=1");
                    exit;
                }
            } else { $msg = "format"; }
        } else { $msg = "photo"; }
    } catch (PDOException $e) { $msg = "db"; }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Ouvrier - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard_agriculteur.css?v=2">
    <style>
        :root {
            --gold: #ffd700;
            --gold-hover: #e6c200;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .register-container {
            display: flex;
            max-width: 1000px;
            width: 95%;
            margin: 20px auto;
            background: rgba(15, 32, 39, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            min-height: 600px;
            animation: fadeInUp 0.7s ease-out;
        }

        .register-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .register-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--gold);
            margin-bottom: 15px;
        }

        .register-left p {
            color: #a0aec0;
            font-size: 14px;
            line-height: 1.8;
        }

        .register-left img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 16px;
            margin-top: 25px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .register-right {
            flex: 1.2;
            padding: 35px;
            overflow-y: auto;
            max-height: 85vh;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            color: #fff;
            margin-bottom: 25px;
            font-size: 26px;
            text-align: center;
            position: relative;
            padding-bottom: 12px;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), transparent);
        }

        .input-group { 
            margin-bottom: 15px; 
        }
        
        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--gold);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 14px;
            color: #f0f4f8;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus, .input-group textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 12px rgba(255, 215, 0, 0.12);
        }

        .input-group input::placeholder, .input-group textarea::placeholder {
            color: #a0aec0;
        }

        .btn-area {
            text-align: center;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--gold), var(--gold-hover));
            color: #0f2027;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.35);
        }

        .alert { 
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
            border: 1px solid rgba(255, 82, 82, 0.3);
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            color: #a0aec0;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                margin: 15px auto;
            }
            .register-left {
                padding: 25px;
                min-height: 200px;
            }
            .register-right {
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="index.php" style="background: rgba(255,215,0,0.1); border: 1px solid rgba(255,215,0,0.2); padding: 8px 18px; border-radius: 8px;">Accueil</a>
        <a href="login.php">Connexion</a>
    </nav>
</header>

<div class="register-container">
    <div class="register-left">
        <h1> Inscription Ouvrier</h1>
        <p>Postulez aux offres et gérez vos missions facilement.</p>
        <img src="photo4.png" alt="Ouvrier">
    </div>

    <div class="register-right">
        <h2 class="form-title">Créer un compte</h2>

        <?php if($msg == "format") echo "<div class='alert'> JPG ou PNG uniquement.</div>"; ?>
        <?php if($msg == "photo") echo "<div class='alert'> La photo est obligatoire.</div>"; ?>

        <form action="register_ouvrier.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" name="nom" placeholder="Nom" required>
            </div>
            <div class="input-group">
                <input type="text" name="prenom" placeholder="Prénom" required>
            </div>
            <div class="input-group">
                <input type="text" name="cin" placeholder="CIN (8 chiffres)" required maxlength="8">
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="text" name="pseudo" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <div class="input-group">
                <textarea name="description" placeholder="Votre expérience..." rows="2"></textarea>
            </div>
            <div class="input-group">
                <label>Photo de profil</label>
                <input type="file" name="photo" accept="image/jpeg, image/png" required style="padding: 10px; font-size: 13px;">
            </div>

            <div class="btn-area">
                <button type="submit" class="btn-submit">S'inscrire</button>
            </div>
            
            <p class="login-link">
                Déjà inscrit ? <a href="login.php">Se connecter</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
