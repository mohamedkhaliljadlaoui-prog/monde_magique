<?php
// ==============================================
// API D'AUTHENTIFICATION
// ==============================================

require_once 'config.php';

// Démarrer la session
session_start();

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Traiter l'action demandée
switch ($action) {
    case 'register':
        if ($method === 'POST') {
            register();
        }
        break;
    
    case 'login':
        if ($method === 'POST') {
            login();
        }
        break;
    
    case 'logout':
        logout();
        break;
    
    case 'check_session':
        check_session();
        break;
    
    case 'get_user_data':
        get_user_data();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action non reconnue']);
}

// ===== FONCTION REGISTER =====
function register() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $conn->real_escape_string($data['username'] ?? '');
    $email = $conn->real_escape_string($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $password_confirm = $data['password_confirm'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        return echo_json(['error' => 'Tous les champs sont requis']);
    }
    
    if ($password !== $password_confirm) {
        http_response_code(400);
        return echo_json(['error' => 'Les mots de passe ne correspondent pas']);
    }
    
    if (strlen($password) < 6) {
        http_response_code(400);
        return echo_json(['error' => 'Le mot de passe doit avoir au moins 6 caractères']);
    }
    
    // Vérifier si l'utilisateur existe
    $check = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        http_response_code(400);
        return echo_json(['error' => 'Cet utilisateur ou email existe déjà']);
    }
    
    // Créer l'utilisateur
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    
    if ($conn->query($sql)) {
        $user_id = $conn->insert_id;
        
        // Créer les entrées de progression pour les 10 stages
        for ($i = 1; $i <= 10; $i++) {
            $conn->query("INSERT INTO progress (user_id, stage_num) VALUES ($user_id, $i)");
        }
        
        // Créer les récompenses
        $conn->query("INSERT INTO rewards (user_id) VALUES ($user_id)");
        
        // Définir la session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        
        http_response_code(201);
        echo_json(['success' => 'Inscription réussie', 'user_id' => $user_id, 'username' => $username]);
    } else {
        http_response_code(500);
        echo_json(['error' => 'Erreur lors de l\'inscription: ' . $conn->error]);
    }
}

// ===== FONCTION LOGIN =====
function login() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $conn->real_escape_string($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        http_response_code(400);
        return echo_json(['error' => 'Identifiant et mot de passe requis']);
    }
    
    // Chercher l'utilisateur
    $result = $conn->query("SELECT id, password, username FROM users WHERE username='$username' OR email='$username'");
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        return echo_json(['error' => 'Identifiant ou mot de passe incorrect']);
    }
    
    $user = $result->fetch_assoc();
    
    // Vérifier le mot de passe
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        return echo_json(['error' => 'Identifiant ou mot de passe incorrect']);
    }
    
    // Mettre à jour last_login
    $user_id = $user['id'];
    $conn->query("UPDATE users SET last_login=NOW() WHERE id=$user_id");
    
    // Définir la session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $user['username'];
    
    http_response_code(200);
    echo_json(['success' => 'Connexion réussie', 'user_id' => $user_id, 'username' => $user['username']]);
}

// ===== FONCTION LOGOUT =====
function logout() {
    session_destroy();
    http_response_code(200);
    echo_json(['success' => 'Déconnexion réussie']);
}

// ===== FONCTION CHECK_SESSION =====
function check_session() {
    if (isset($_SESSION['user_id'])) {
        echo_json(['logged_in' => true, 'user_id' => $_SESSION['user_id'], 'username' => $_SESSION['username']]);
    } else {
        echo_json(['logged_in' => false]);
    }
}

// ===== FONCTION GET_USER_DATA =====
function get_user_data() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        return echo_json(['error' => 'Non authentifié']);
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Récupérer les récompenses
    $rewards = $conn->query("SELECT * FROM rewards WHERE user_id=$user_id")->fetch_assoc();
    
    // Récupérer la progression de tous les stages
    $progress = [];
    $result = $conn->query("SELECT * FROM progress WHERE user_id=$user_id ORDER BY stage_num");
    while ($row = $result->fetch_assoc()) {
        $progress[$row['stage_num']] = $row;
    }
    
    echo_json([
        'user_id' => $user_id,
        'username' => $_SESSION['username'],
        'rewards' => $rewards,
        'progress' => $progress
    ]);
}

// ===== FONCTION UTILITAIRE =====
function echo_json($data) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

?>
