<?php
session_start();
include("config.php");

// Vérifier connexion (Rôle agriculteur)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "agriculteur"){
    header("Location: login.php");
    exit();
}

$agriculteur_id = $_SESSION['user_id'];
$error = "";

// Valeurs du formulaire (pour conservation après erreur)
$val_id_fruit = $_POST['id_type_fruit'] ?? "";
$val_id_gouv = $_POST['id_gouvernorat'] ?? "";
$val_adresse = $_POST['adresse'] ?? "";
$val_date_debut = $_POST['date_debut'] ?? "";
$val_date_fin = $_POST['date_fin'] ?? "";
$val_ouvriers = $_POST['nombre_ouvriers'] ?? "";
$val_prix = $_POST['prix_journee'] ?? "";
$val_date_limite = $_POST['date_limite'] ?? "";

// Récupération dynamique des données pour les listes
try {
    $stmt_fruits = $conn->query("SELECT id_type_fruit, libelle FROM type_fruit ORDER BY libelle ASC");
    $fruits = $stmt_fruits->fetchAll(PDO::FETCH_ASSOC);

    $stmt_gouv = $conn->query("SELECT id_gouvernorat, libelle FROM gouvernorat ORDER BY libelle ASC");
    $gouvernorats = $stmt_gouv->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = " Erreur de chargement des listes : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_type_fruit = $_POST['id_type_fruit'] ?? "";
    $id_gouvernorat = $_POST['id_gouvernorat'] ?? "";
    $adresse = trim($_POST['adresse'] ?? "");
    $date_debut = $_POST['date_debut'] ?? "";
    $date_fin = $_POST['date_fin'] ?? "";
    $nombre_ouvriers = (int) ($_POST['nombre_ouvriers'] ?? 0);
    $prix_journee = (float) ($_POST['prix_journee'] ?? 0);
    $date_limite = $_POST['date_limite'] ?? "";

    if (empty($id_type_fruit)) {
        $error = " Veuillez choisir un type de fruit.";
    }
    elseif (empty($id_gouvernorat)) {
        $error = " Veuillez choisir un gouvernorat.";
    }
    elseif (empty($adresse)) {
        $error = " Veuillez indiquer l'adresse exacte.";
    }
    elseif (empty($date_debut)) {
        $error = " Veuillez indiquer la date de début.";
    }
    elseif (empty($date_fin)) {
        $error = " Veuillez indiquer la date de fin.";
    }
    elseif ($nombre_ouvriers <= 0) {
        $error = " Le nombre d'ouvriers doit être supérieur à 0.";
    }
    elseif ($prix_journee <= 0) {
        $error = " Le prix par journée doit être supérieur à 0.";
    }
    elseif (empty($date_limite)) {
        $error = " Veuillez indiquer la date limite de candidature.";
    }
    elseif ($date_debut > $date_fin) {
        $error = " La date de début doit être antérieure à la date de fin";
    }
    else {
        try {
            $sql = "INSERT INTO offre 
            (id_type_fruit, id_gouvernorat, adresse, date_debut, date_fin, nombre_ouvriers, prix_journee, date_limite, id_agriculteur)
            VALUES 
            (:id_type_fruit, :id_gouvernorat, :adresse, :date_debut, :date_fin, :nombre_ouvriers, :prix_journee, :date_limite, :id_agriculteur)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id_type_fruit'  => $id_type_fruit,
                ':id_gouvernorat' => $id_gouvernorat,
                ':adresse'        => $adresse,
                ':date_debut'     => $date_debut,
                ':date_fin'       => $date_fin,
                ':nombre_ouvriers'=> $nombre_ouvriers,
                ':prix_journee'   => $prix_journee,
                ':date_limite'    => $date_limite,
                ':id_agriculteur' => $agriculteur_id
            ]);

            header("Location: dashboard_agriculteur.php");
            exit();

        } catch (PDOException $e) {
            $error = " Erreur SQL : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une offre - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="ajouter_offre.css?v=2">
</head>
<body>

<header>
    <h1> 🌿 Uber-Cueillette </h1>
    <nav>
        <a href="dashboard_agriculteur.php">Dashboard</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="form-container">
    <h2 class="form-title"> Publier une offre</h2>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-grid">
        <div class="input-group">
            <label>Type de fruit</label>
            <select name="id_type_fruit" required>
                <option value="" <?= empty($val_id_fruit) ? 'selected' : '' ?>>Choisir un fruit</option>
                <?php foreach($fruits as $fruit): ?>
                    <option value="<?php echo $fruit['id_type_fruit']; ?>" <?= $val_id_fruit == $fruit['id_type_fruit'] ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($fruit['libelle']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(empty($fruits)): ?>
                <small style="color: #ff5252;"> Aucun fruit disponible. <a href="admin.php" style="color: var(--accent-gold);">Ajouter un fruit</a></small>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label>Gouvernorat</label>
            <select name="id_gouvernorat" required>
                <option value="" <?= empty($val_id_gouv) ? 'selected' : '' ?>>Choisir une région</option>
                <?php foreach($gouvernorats as $gouv): ?>
                    <option value="<?php echo $gouv['id_gouvernorat']; ?>" <?= $val_id_gouv == $gouv['id_gouvernorat'] ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($gouv['libelle']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(empty($gouvernorats)): ?>
                <small style="color: #ff5252;"> Aucun gouvernorat disponible. <a href="admin.php" style="color: var(--accent-gold);">Ajouter un gouvernorat</a></small>
            <?php endif; ?>
        </div>

        <div class="input-group full-width">
            <label>Lieu exact</label>
            <input type="text" name="adresse" value="<?= htmlspecialchars($val_adresse) ?>" placeholder="Ex: Ferme El Hana, Route de Tunis" required>
        </div>

        <div class="input-group">
            <label>Date début <small style="color: var(--text-muted); font-weight: 400;">(ex: 15/06/2025)</small></label>
            <input type="date" name="date_debut" value="<?= htmlspecialchars($val_date_debut) ?>" required min="<?= date('Y-m-d') ?>">
        </div>

        <div class="input-group">
            <label>Date fin <small style="color: var(--text-muted); font-weight: 400;">(ex: 30/06/2025)</small></label>
            <input type="date" name="date_fin" value="<?= htmlspecialchars($val_date_fin) ?>" required min="<?= date('Y-m-d') ?>">
        </div>

        <div class="input-group">
            <label>Ouvriers nécessaires</label>
            <input type="number" name="nombre_ouvriers" value="<?= htmlspecialchars($val_ouvriers) ?>" placeholder="Ex: 5" required min="1">
        </div>

        <div class="input-group">
            <label>Prix/Jour (DT)</label>
            <input type="number" step="0.01" name="prix_journee" value="<?= htmlspecialchars($val_prix) ?>" placeholder="Ex: 45.50" required min="0">
        </div>

        <div class="input-group full-width">
            <label>Date limite candidature <small style="color: var(--text-muted); font-weight: 400;">(ex: 10/06/2025)</small></label>
            <input type="date" name="date_limite" value="<?= htmlspecialchars($val_date_limite) ?>" required min="<?= date('Y-m-d') ?>">
        </div>

        <button type="submit" class="btn-submit"> Publier l'offre</button>
    </form>
</div>

</body>
</html>
