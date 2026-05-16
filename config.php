<?php
// ==============================================
// CONFIGURATION BASE DE DONNÉES
// ==============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mondo_magique');

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Si la base de données n'existe pas, la créer
$sql_create_db = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (!$conn->query($sql_create_db)) {
    die(json_encode(['error' => 'Erreur création BD: ' . $conn->error]));
}

// Sélectionner la base de données
$conn->select_db(DB_NAME);

// Créer les tables si elles n'existent pas
$tables_sql = [
    // Table des utilisateurs
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP
    )",
    
    // Table de la progression des stages
    "CREATE TABLE IF NOT EXISTS progress (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        stage_num INT NOT NULL,
        completed BOOLEAN DEFAULT FALSE,
        qcm_score INT DEFAULT 0,
        essay_score INT DEFAULT 0,
        diamonds INT DEFAULT 0,
        coins INT DEFAULT 0,
        last_step INT DEFAULT 1,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_progress (user_id, stage_num),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Table des récompenses totales
    "CREATE TABLE IF NOT EXISTS rewards (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL UNIQUE,
        total_diamonds INT DEFAULT 0,
        total_coins INT DEFAULT 0,
        total_stages_completed INT DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Table des réponses aux QCM
    "CREATE TABLE IF NOT EXISTS qcm_answers (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        stage_num INT NOT NULL,
        q1_answer CHAR(1),
        q2_answer CHAR(1),
        q3_answer CHAR(1),
        q4_answer CHAR(1),
        q5_answer CHAR(1),
        q1_correct BOOLEAN DEFAULT FALSE,
        q2_correct BOOLEAN DEFAULT FALSE,
        q3_correct BOOLEAN DEFAULT FALSE,
        q4_correct BOOLEAN DEFAULT FALSE,
        q5_correct BOOLEAN DEFAULT FALSE,
        replied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_answers (user_id, stage_num),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Table des essais
    "CREATE TABLE IF NOT EXISTS essays (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        stage_num INT NOT NULL,
        content LONGTEXT,
        word_count INT DEFAULT 0,
        score INT DEFAULT 0,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_essay (user_id, stage_num),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables_sql as $sql) {
    if (!$conn->query($sql)) {
        die(json_encode(['error' => 'Erreur création table: ' . $conn->error]));
    }
}

// Définir le timezone
date_default_timezone_set('UTC');

// Headers pour JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

?>
