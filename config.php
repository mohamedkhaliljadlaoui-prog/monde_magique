<?php
// ==============================================
// CONFIGURATION BASE DE DONNÉES
// ==============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mondo_magique');

// Optionnel: port MySQL (utile si XAMPP utilise 3307/3308)
if (!defined('DB_PORT')) {
    $envPort = getenv('DB_PORT');
    define('DB_PORT', $envPort ? (int)$envPort : 3306);
}

// Connexion à la base de données (sans warnings qui cassent le JSON)
mysqli_report(MYSQLI_REPORT_OFF);

$conn = null;
$portsToTry = array_values(array_unique([DB_PORT, 3306, 3307, 3308]));
foreach ($portsToTry as $tryPort) {
    $tmp = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', (int)$tryPort);
    if ($tmp && empty($tmp->connect_error)) {
        $conn = $tmp;
        break;
    }
}

if (!$conn || !empty($conn->connect_error)) {
    $msg = 'Impossible de se connecter à la base de données';
    $details = $conn ? $conn->connect_error : 'mysqli connection failed';
    http_response_code(500);
    // Ne pas dépendre des headers JSON ici: renvoyer du JSON propre
    echo json_encode([
        'success' => false,
        'error' => $msg,
        'details' => $details,
        'host' => DB_HOST,
        'user' => DB_USER,
        'db' => DB_NAME,
        'ports_tried' => $portsToTry,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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
        parent_email VARCHAR(100) NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL DEFAULT NULL
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

// Migrations légères (ajout de colonnes manquantes)
$colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'parent_email'");
if ($colCheck && $colCheck->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN parent_email VARCHAR(100) NULL AFTER email");
}

// Définir le timezone
date_default_timezone_set('UTC');

// Headers JSON/CORS seulement pour les endpoints API
$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isApi = in_array($script, ['auth.php', 'progress_api.php', 'parent_api.php'], true);
if ($isApi) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

?>
