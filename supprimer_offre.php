<?php
session_start();
require_once("config.php");

// Vérification de l'accès
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "agriculteur") {
    header("Location: login.php");
    exit;
}

$id_agri = $_SESSION['user_id'];
$id_offre = $_GET['id'] ?? null;

if ($id_offre) {
    try {
        // 1. Vérifier si l'offre appartient à cet agriculteur
        // ET compter combien d'ouvriers sont déjà acceptés
        $check = $conn->prepare("SELECT 
            (SELECT COUNT(*) FROM candidature WHERE id_offre = ? AND decision = 'Accepte') as nb_acceptes,
            id_agriculteur 
            FROM offre WHERE id_offre = ?");
        $check->execute([$id_offre, $id_offre]);
        $resultat = $check->fetch();

        if ($resultat && $resultat['id_agriculteur'] == $id_agri) {
            if ($resultat['nb_acceptes'] == 0) {
                // 2. Supprimer les candidatures liées (en cours ou refusées) 
                // pour éviter les erreurs de clé étrangère
                $del_cand = $conn->prepare("DELETE FROM candidature WHERE id_offre = ?");
                $del_cand->execute([$id_offre]);

                // 3. Supprimer l'offre
                $del_offre = $conn->prepare("DELETE FROM offre WHERE id_offre = ?");
                $del_offre->execute([$id_offre]);

                header("Location: dashboard_agriculteur.php?msg=deleted");
            } else {
                // Impossible de supprimer car des ouvriers sont déjà engagés
                header("Location: dashboard_agriculteur.php?msg=has_workers");
            }
        } else {
            header("Location: dashboard_agriculteur.php");
        }
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: dashboard_agriculteur.php");
}
exit;