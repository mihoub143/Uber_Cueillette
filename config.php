<?php

// =========================
// CONFIG DB (Render + FreeSQLDatabase)
// =========================

$host = getenv("DB_HOST") ?: "sql7.freesqldatabase.com";
$dbname = getenv("DB_NAME") ?: "sql7824394";
$user = getenv("DB_USER") ?: "sql7824394";
$password = getenv("DB_PASSWORD") ?: "CVNwiFKb7g";
$port = getenv("DB_PORT") ?: "3306";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8",
        $user,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erreur connexion base de données : " . $e->getMessage());
}

?>