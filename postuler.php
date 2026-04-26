<?php
session_start();
require_once("config.php");

// Sécurité : Vérifier si l'utilisateur est un ouvrier connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "ouvrier") {
    header("Location: login.php");
    exit;
}

$id_ouvrier = $_SESSION['user_id'];
$id_offre = $_GET['id'] ?? null;

if ($id_offre) {
    try {
        // 1. Vérifier si l'ouvrier a déjà postulé (double sécurité)
        $check = $conn->prepare("SELECT id_candidature FROM candidature WHERE id_offre = ? AND id_ouvrier = ?");
        $check->execute([$id_offre, $id_ouvrier]);
        
        if ($check->rowCount() == 0) {
            // 2. Insérer la candidature (Statut par défaut : En cours)
            $sql = "INSERT INTO candidature (id_offre, id_ouvrier, decision, date_candidature) 
                    VALUES (?, ?, 'En cours', CURRENT_TIMESTAMP)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_offre, $id_ouvrier]);
            
            // Redirection avec succès
            header("Location: dashboard_ouvrier.php?msg=success");
        } else {
            // Déjà postulé
            header("Location: dashboard_ouvrier.php?msg=already");
        }
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: dashboard_ouvrier.php");
}
exit;