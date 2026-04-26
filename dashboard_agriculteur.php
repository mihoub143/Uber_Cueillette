<?php
session_start();
require_once("config.php");

// 1. Vérification de l'accès (Rôle agriculteur uniquement)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "agriculteur") {
    header("Location: login.php");
    exit;
}

$id_agri = $_SESSION['user_id'];

// 2. Récupération des informations du profil de l'agriculteur
$stmt_profil = $conn->prepare("SELECT * FROM agriculteur WHERE id_agriculteur = ?");
$stmt_profil->execute([$id_agri]);
$profil = $stmt_profil->fetch(PDO::FETCH_ASSOC);

// 3. Récupération des offres de l'agriculteur
$sql = "SELECT o.*, f.libelle as fruit, g.libelle as gouv 
        FROM offre o 
        JOIN type_fruit f ON o.id_type_fruit = f.id_type_fruit
        JOIN gouvernorat g ON o.id_gouvernorat = g.id_gouvernorat
        WHERE o.id_agriculteur = ?
        ORDER BY o.date_limite DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_agri]);
$offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agriculteur - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard_agriculteur.css?v=2">
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="dashboard_agriculteur.php">Mon Dashboard</a>
        <a href="ajouter_offre.php">Publier une Offre</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="container">
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-alert <?= ($_GET['msg'] == 'deleted') ? 'msg-success' : 'msg-error' ?>">
            <?php 
                if($_GET['msg'] == 'deleted') echo " L'offre a été supprimée avec succès.";
                if($_GET['msg'] == 'has_workers') echo " Suppression impossible : des ouvriers ont déjà été acceptés.";
            ?>
        </div>
    <?php endif; ?>

    <section id="profil" class="section-box">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h2>Bienvenue, <?= htmlspecialchars($profil['nom'] . ' ' . $profil['prenom']) ?> </h2>
                <p style="margin-top: 10px; color: var(--text-muted);"><b style="color: var(--accent-gold);"> Email :</b> <?= htmlspecialchars($profil['email']) ?></p>
                <p style="color: var(--text-muted);"><b style="color: var(--accent-gold);"> Adresse :</b> <?= htmlspecialchars($profil['adresse']) ?></p>
            </div>
            <a href="modifier_profil_agri.php">
                <button class="btn">Modifier mon profil</button>
            </a>
        </div>
    </section>

    <section id="mesoffres" class="section-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <h2 style="font-family: 'Playfair Display', serif; color: #fff; font-size: 24px;"> Mes récoltes en cours</h2>
            <a href="ajouter_offre.php" style="text-decoration: none;">
                <button class="btn">+ Nouvelle Offre</button>
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fruit</th>
                        <th>Région</th>
                        <th>Recrutement (Admis/Total)</th>
                        <th>Salaire/Jour</th>
                        <th>Date Limite</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offres as $o): 
                        // Compter les ouvriers déjà acceptés pour cette offre
                        $st = $conn->prepare("SELECT COUNT(*) FROM candidature WHERE id_offre = ? AND decision = 'Accepte'");
                        $st->execute([$o['id_offre']]);
                        $acceptes = $st->fetchColumn();
                        
                        $expiree = (strtotime($o['date_limite']) < time());
                        $complet = ($acceptes >= $o['nombre_ouvriers']);
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($o['fruit']) ?></td>
                        <td><?= htmlspecialchars($o['gouv']) ?></td>
                        <td>
                            <span class="<?= $complet ? 'status-complet' : 'status-ouvert' ?>">
                                <?= $acceptes ?> / <?= $o['nombre_ouvriers'] ?>
                            </span>
                            <?php if ($complet || $expiree): ?>
                                <br><span class="badge-cloture">OFFRE CLÔTURÉE</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: var(--accent-gold); font-weight: 600;"><?= number_format($o['prix_journee'], 2) ?> DT</td>
                        <td><?= date('d/m/Y', strtotime($o['date_limite'])) ?></td>
                        <td>
                            <a href="voir_postulants.php?id=<?= $o['id_offre'] ?>">
                                <button class="btn" style="padding: 5px 12px; font-size: 12px;">Gérer Postulants</button>
                            </a>
                            
                            <?php if($acceptes == 0): ?>
                                <a href="supprimer_offre.php?id=<?= $o['id_offre'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette offre ?')">
                                    <button class="btn delete" style="padding: 5px 12px; font-size: 12px; margin-top: 5px;">Supprimer</button>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($offres)): ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            Vous n'avez publié aucune offre de récolte pour le moment.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

</body>
</html>
