<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "ouvrier") {
    header("Location: login.php");
    exit;
}

$id_ouv = $_SESSION['user_id'];

// 1. RÉCUPÉRER LES OFFRES DISPONIBLES (simplifié pour debug)
$sql_dispo = "SELECT o.*, f.libelle as fruit, g.libelle as gouv
             FROM offre o 
             JOIN type_fruit f ON o.id_type_fruit = f.id_type_fruit
             JOIN gouvernorat g ON o.id_gouvernorat = g.id_gouvernorat
             WHERE o.date_limite >= CURDATE() 
             AND o.id_offre NOT IN (SELECT id_offre FROM candidature WHERE id_ouvrier = ?)";

$stmt_dispo = $conn->prepare($sql_dispo);
$stmt_dispo->execute([$id_ouv]);
$offres_disponibles = $stmt_dispo->fetchAll(PDO::FETCH_ASSOC);

// Debug: compter toutes les offres
$stmt_all = $conn->query("SELECT COUNT(*) as total FROM offre");
$total_offres = $stmt_all->fetchColumn();

$stmt_mine = $conn->prepare("SELECT COUNT(*) as total FROM candidature WHERE id_ouvrier = ?");
$stmt_mine->execute([$id_ouv]);
$mes_postulations = $stmt_mine->fetchColumn();

// 2. RÉCUPÉRER LES OFFRES POSTULÉES
$sql_postule = "SELECT c.*, o.prix_journee, f.libelle as fruit, g.libelle as gouv
                FROM candidature c
                JOIN offre o ON c.id_offre = o.id_offre
                JOIN type_fruit f ON o.id_type_fruit = f.id_type_fruit
                JOIN gouvernorat g ON o.id_gouvernorat = g.id_gouvernorat
                WHERE c.id_ouvrier = ? AND (c.notr IS NULL OR c.notr = 0)";

$stmt_postule = $conn->prepare($sql_postule);
$stmt_postule->execute([$id_ouv]);
$mes_candidatures = $stmt_postule->fetchAll(PDO::FETCH_ASSOC);

// 3. RÉCUPÉRER LES CHANTIERS CLÔTURÉS
$sql_cloture = "SELECT c.*, f.libelle as fruit, o.adresse
                FROM candidature c
                JOIN offre o ON c.id_offre = o.id_offre
                JOIN type_fruit f ON o.id_type_fruit = f.id_type_fruit
                WHERE c.id_ouvrier = ? AND c.decision = 'Accepte' AND c.notr > 0";

$stmt_cloture = $conn->prepare($sql_cloture);
$stmt_cloture->execute([$id_ouv]);
$mes_chantiers = $stmt_cloture->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Ouvrier - AgriConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard_ouvrier.css?v=2">
</head>
<body>

<header>
    <h1>🌿 Uber-Cueillette</h1>
    <nav>
        <a href="#offres">Offres Disponibles</a>
        <a href="#postule">Mes Postulations</a>
        <a href="#cloture">Historique/Gains</a>
        <a href="modifier_profil_ouvrier.php">Mon Profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<div class="container">

    <section id="offres" class="section-box">
        <h2> Offres de récolte disponibles</h2>
        
        <!-- DEBUG INFO -->
        <div style="background: rgba(255,215,0,0.1); border: 1px solid rgba(255,215,0,0.3); border-radius: 8px; padding: 10px 15px; margin-bottom: 15px; font-size: 13px; color: var(--text-muted);">
             <b>Debug:</b> Total offres en BDD: <b style="color: var(--accent-gold);"><?= $total_offres ?></b> | 
            Mes postulations: <b style="color: var(--accent-gold);"><?= $mes_postulations ?></b> | 
            Offres visibles: <b style="color: var(--accent-gold);"><?= count($offres_disponibles) ?></b>
        </div>
        
        <div class="grid">
            <?php foreach($offres_disponibles as $o): ?>
            <div class="card">
                <h3><?= htmlspecialchars($o['fruit']) ?></h3>
                <p> <?= htmlspecialchars($o['gouv']) ?> (<?= htmlspecialchars($o['adresse']) ?>)</p>
                <p style="color: var(--accent-gold); font-weight: 600; font-size: 18px;"> <?= $o['prix_journee'] ?> DT / jour</p>
                <p style="color: var(--text-muted); font-size: 13px;"> Jusqu'au : <?= date('d/m', strtotime($o['date_limite'])) ?></p>
                <a href="postuler.php?id=<?= $o['id_offre'] ?>">
                    <button style="width:100%; padding: 10px; background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-hover)); color: var(--primary-dark); border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; font-weight: 600; font-family: 'Poppins', sans-serif; transition: all 0.3s;">Postuler en 1 clic</button>
                </a>
            </div>
            <?php endforeach; ?>
            <?php if(empty($offres_disponibles)) echo "<p style='color: var(--text-muted); text-align: center; width: 100%; padding: 20px;'>Aucune nouvelle offre disponible pour le moment.</p>"; ?>
        </div>
    </section>

    <section id="postule" class="section-box">
        <h2> Mes candidatures envoyées</h2>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Récolte</th>
                        <th>Région</th>
                        <th>Prix</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mes_candidatures as $c): ?>
                    <tr>
                        <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($c['fruit']) ?></td>
                        <td><?= htmlspecialchars($c['gouv']) ?></td>
                        <td style="color: var(--accent-gold); font-weight: 600;"><?= $c['prix_journee'] ?> DT</td>
                        <td><span class="status-badge <?= str_replace(' ', '-', $c['decision']) ?>"><?= $c['decision'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="cloture" class="section-box">
        <h2> Chantiers terminés & Gains</h2>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Chantier</th>
                        <th>Note reçue</th>
                        <th>Rémunération</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mes_chantiers as $m): ?>
                    <tr>
                        <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($m['fruit']) ?></td>
                        <td><b style="color: var(--accent-gold);"> <?= $m['notr'] ?>/10</b></td>
                        <td style="color: var(--success); font-weight: 600;"><?= $m['remuneration'] ?> DT</td>
                        <td style="font-style: italic; color: var(--text-muted);">"<?= htmlspecialchars($m['commentaire']) ?>"</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

</body>
</html>
