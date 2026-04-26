<?php
session_start();
include("config.php");

$message = "";

// 1. Ajouter un Type de Fruit
if (isset($_POST['add_fruit'])) {
    $libelle = trim($_POST['libelle_fruit']);
    if (!empty($libelle)) {
        try {
            $stmt = $conn->prepare("INSERT INTO type_fruit (libelle) VALUES (:libelle)");
            $stmt->execute([':libelle' => $libelle]);
            $message = "✅ Fruit ajouté avec succès !";
        } catch (PDOException $e) {
            $message = "❌ Erreur : " . $e->getMessage();
        }
    }
}

// 2. Ajouter un Gouvernorat
if (isset($_POST['add_gouv'])) {
    $libelle = trim($_POST['libelle_gouv']);
    if (!empty($libelle)) {
        try {
            $stmt = $conn->prepare("INSERT INTO gouvernorat (libelle) VALUES (:libelle)");
            $stmt->execute([':libelle' => $libelle]);
            $message = "✅ Gouvernorat ajouté avec succès !";
        } catch (PDOException $e) {
            $message = "❌ Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="ajouter_offre.css?v=2">
    <style>
        .admin-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
            margin-top: 20px;
        }
        
        .admin-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 30px; 
            border-radius: 16px; 
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            border-color: rgba(255, 215, 0, 0.2);
        }
        
        .admin-card h3 { 
            color: var(--accent-gold); 
            margin-bottom: 20px; 
            font-family: 'Playfair Display', serif;
            font-size: 20px;
        }

        .admin-card .input-group {
            margin-bottom: 15px;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="dashboard_agriculteur.php">Dashboard</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="form-container">
    <h2 class="form-title"> Panneau d'Administration</h2>
    
    <?php if($message): ?>
        <div class="alert <?= strpos($message, '✅') !== false ? 'alert-success' : 'alert-error' ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="admin-grid">
        <div class="admin-card">
            <h3> Ajouter un Fruit</h3>
            <form method="POST">
                <div class="input-group full-width">
                    <input type="text" name="libelle_fruit" placeholder="Nom du fruit (ex: Dates, Pommes)" required>
                </div>
                <button type="submit" name="add_fruit" class="btn-submit" style="margin-top: 5px;">Enregistrer</button>
            </form>
        </div>

        <div class="admin-card">
            <h3> Ajouter un Gouvernorat</h3>
            <form method="POST">
                <div class="input-group full-width">
                    <input type="text" name="libelle_gouv" placeholder="Nom du gouvernorat (ex: Béja, Jendouba)" required>
                </div>
                <button type="submit" name="add_gouv" class="btn-submit" style="margin-top: 5px;">Enregistrer</button>
            </form>
        </div>
    </div>

    <p class="back-link">
        <a href="ajouter_offre.php">← Retour aux offres</a>
    </p>
</div>

</body>
</html>
