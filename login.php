<?php
ob_start();
session_start();
include("config.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pseudo = isset($_POST['pseudo']) ? trim($_POST['pseudo']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if (!empty($pseudo) && !empty($password)) {
        try {
            $user = null;
            $role = "";
            $redirect = "";
            $id_column = ""; 

            // 1. Recherche Agriculteur
            $sql1 = "SELECT * FROM agriculteur WHERE pseudo = :p";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([':p' => $pseudo]);
            $res = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($res) {
                $user = $res;
                $role = "agriculteur";
                $redirect = "dashboard_agriculteur.php";
                $id_column = "id_agriculteur";
            } else {
                // 2. Recherche Ouvrier
                $sql2 = "SELECT * FROM ouvrier WHERE pseudo = :p";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->execute([':p' => $pseudo]);
                $res = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($res) {
                    $user = $res;
                    $role = "ouvrier";
                    $redirect = "dashboard_ouvrier.php";
                    $id_column = "id_ouvrier";
                }
            }

            if ($user) {
                // Vérification du mot de passe (Clair ou Haché)
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user[$id_column]; 
                    $_SESSION['pseudo'] = $user['pseudo'];
                    $_SESSION['role'] = $role;

                    header("Location: " . $redirect);
                    exit();
                } else {
                    $error = "Mot de passe incorrect.";
                }
            } else {
                $error = "Utilisateur introuvable.";
            }

        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AgriConnect</title>
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

        /* LOGIN CONTAINER */
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 95%;
            margin: auto;
            background: rgba(15, 32, 39, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            min-height: 550px;
            animation: fadeInUp 0.7s ease-out;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 60%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: var(--gold);
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .login-left p {
            color: #a0aec0;
            font-size: 14px;
            line-height: 1.8;
            position: relative;
            z-index: 1;
        }

        .login-left img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 16px;
            margin-top: 25px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        .login-right {
            flex: 1.2;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-family: 'Playfair Display', serif;
            color: #fff;
            margin-bottom: 30px;
            font-size: 32px;
            text-align: center;
            position: relative;
        }

        .login-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), transparent);
        }

        .input-group { 
            margin-bottom: 22px; 
            position: relative;
        }
        
        .input-group input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 15px;
            color: #f0f4f8;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.15);
        }

        .input-group input::placeholder {
            color: #a0aec0;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
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
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.35);
        }

        .error-box {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252; 
            padding: 14px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            border: 1px solid rgba(255, 82, 82, 0.3);
            text-align: center;
            font-weight: 500;
            font-size: 14px;
        }

        .register-links {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #a0aec0;
        }

        .register-links a {
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .register-links a:hover {
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 20px auto;
            }
            .login-left {
                padding: 30px;
                min-height: 250px;
            }
            .login-right {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="index.php" style="background: rgba(255,215,0,0.1); border: 1px solid rgba(255,215,0,0.2); padding: 8px 18px; border-radius: 8px;">Accueil</a>
    </nav>
</header>

<div class="login-container">
    <div class="login-left">
        <h1>Bon retour !</h1>
        <p>Accédez à votre espace pour gérer vos offres ou vos missions de récolte.</p>
        <img src="photo2.png" alt="Champ">
    </div>

    <div class="login-right">
        <h2 class="login-title">Connexion</h2>

        <?php if (!empty($error)): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="input-group">
                <input type="text" name="pseudo" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <button type="submit" class="btn-submit">Se connecter</button>

            <div class="register-links">
                Pas encore inscrit ? <br>
                S'inscrire en tant qu' <a href="register_agriculteur.php">Agriculteur</a> ou <a href="register_ouvrier.php">Ouvrier</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
<?php
ob_end_flush();
?>
