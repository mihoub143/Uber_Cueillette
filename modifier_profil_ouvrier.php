<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "ouvrier") {
    header("Location: login.php");
    exit;
}

$id_ouv = $_SESSION['user_id'];
$msg = "";

$stmt = $conn->prepare("SELECT * FROM ouvrier WHERE id_ouvrier = ?");
$stmt->execute([$id_ouv]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $desc = $_POST['description'];

    try {
        $sql = "UPDATE ouvrier SET nom = ?, prenom = ?, email = ?, description = ? WHERE id_ouvrier = ?";
        $params = [$nom, $prenom, $email, $desc, $id_ouv];
        $conn->prepare($sql)->execute($params);

        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoData = file_get_contents($_FILES['photo']['tmp_name']);
            $stmt_img = $conn->prepare("UPDATE ouvrier SET photo = ? WHERE id_ouvrier = ?");
            $stmt_img->bindParam(1, $photoData, PDO::PARAM_LOB);
            $stmt_img->bindParam(2, $id_ouv);
            $stmt_img->execute();
        }

        $msg = " Profil mis à jour avec succès !";
        header("Refresh:1");
    } catch (PDOException $e) {
        $msg = " Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="ajouter_offre.css?v=2">
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="dashboard_ouvrier.php">Dashboard</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="form-container">
    <h2 class="form-title"> Modifier mon profil</h2>

    <?php if($msg): ?>
        <div class="alert <?= strpos($msg, '✅') !== false ? 'alert-success' : 'alert-error' ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="form-grid">
        <div class="input-group">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
        </div>
        
        <div class="input-group">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
        </div>
        
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        
        <div class="input-group full-width">
            <label>Description (Expériences)</label>
            <textarea name="description" rows="3" placeholder="Décrivez votre expérience agricole..."><?= htmlspecialchars($user['description']) ?></textarea>
        </div>
        
        <div class="input-group full-width">
            <label>Nouvelle Photo (Optionnel)</label>
            <input type="file" name="photo" accept="image/*" style="padding: 10px;">
        </div>
        
        <button type="submit" class="btn-submit"> Enregistrer</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="dashboard_ouvrier.php" style="color: var(--accent-gold); text-decoration: none; font-weight: 600;">← Retour au Dashboard</a>
    </p>
</div>

</body>
</html>
