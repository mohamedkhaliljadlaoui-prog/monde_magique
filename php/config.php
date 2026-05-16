<?php
// Configuration base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'monde_magique');

// Connexion PDO
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Erreur connexion BD: ' . $e->getMessage());
}

// Activer les sessions
session_start();

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
?>
