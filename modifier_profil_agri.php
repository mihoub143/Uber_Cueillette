<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "agriculteur") {
    header("Location: login.php");
    exit;
}

$id_agri = $_SESSION['user_id'];
$msg = "";

$stmt = $conn->prepare("SELECT * FROM agriculteur WHERE id_agriculteur = ?");
$stmt->execute([$id_agri]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];

    try {
        $sql = "UPDATE agriculteur SET nom = ?, prenom = ?, email = ?, adresse = ? WHERE id_agriculteur = ?";
        $conn->prepare($sql)->execute([$nom, $prenom, $email, $adresse, $id_agri]);
        $msg = " Informations enregistrées !";
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
        <a href="dashboard_agriculteur.php">Dashboard</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="form-container">
    <h2 class="form-title">👤 Mon Profil</h2>

    <?php if($msg): ?>
        <div class="alert <?= strpos($msg, '✅') !== false ? 'alert-success' : 'alert-error' ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="form-grid">
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
            <label>Adresse du siège</label>
            <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>" required>
        </div>
        
        <button type="submit" class="btn-submit"> Mettre à jour</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="dashboard_agriculteur.php" style="color: var(--accent-gold); text-decoration: none; font-weight: 600;">← Retour au Dashboard</a>
    </p>
</div>

</body>
</html>
