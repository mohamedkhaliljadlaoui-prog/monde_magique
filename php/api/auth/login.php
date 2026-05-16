<?php
// login.php - Authentification des utilisateurs

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once '../../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Valider les données
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur est requis']);
    exit;
}

if (empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le mot de passe est requis']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Détecter quelle colonne de mot de passe existe
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
    $passwordColumn = 'password';
    while ($row = $stmt->fetch()) {
        if ($row['Field'] === 'password_hash') {
            $passwordColumn = 'password_hash';
            break;
        }
    }
    
    // Détecter la colonne de genre
    $stmt = $db->query("SHOW COLUMNS FROM users");
    $columns = [];
    while ($row = $stmt->fetch()) {
        $columns[] = $row['Field'];
    }
    $genderCol = in_array('guide_gender', $columns) ? 'guide_gender' : 'gender';
    
    // Chercher l'utilisateur par username ou email
    $sql = "SELECT id, username, email, {$passwordColumn} as password, {$genderCol} as gender, 
                   level, xp, coins, diamonds, current_stage, guide_name, created_at
            FROM users 
            WHERE username = :username OR email = :username 
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }
    
    // Vérifier le mot de passe
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }
    
    // Mettre à jour la dernière connexion si la colonne existe
    if (in_array('last_login', $columns)) {
        $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $updateStmt->execute([':id' => $user['id']]);
    }
    
    // Générer un token simple
    $token = 'token_' . bin2hex(random_bytes(16));
    
    // Retirer le mot de passe de la réponse
    unset($user['password']);
    
    // Préparer la réponse
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'token' => $token,
        'user' => [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'gender' => $user['gender'],
            'guide_name' => $user['guide_name'] ?? ($user['gender'] === 'boy' ? 'تيو' : 'ليا'),
            'level' => (int)$user['level'],
            'xp' => (int)$user['xp'],
            'coins' => (int)$user['coins'],
            'diamonds' => (int)$user['diamonds'],
            'current_stage' => (int)$user['current_stage'],
            'language' => 'ar'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Erreur PDO login: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('Erreur login: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
        ],
        'token' => $token,
        'session_id' => $sessionId,
        'expires_in' => $remember ? 2592000 : 86400 // 30 jours ou 1 jour
    ];
    
    // Si "se souvenir de moi" est activé, configurer un cookie
    if ($remember) {
        setcookie('remember_token', $token, time() + 2592000, '/', '', true, true);
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log('Erreur connexion: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur est survenue lors de la connexion']);
}

// Fonctions utilitaires
function get_browser_info() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    if (strpos($user_agent, 'Firefox') !== false) {
        return 'Firefox';
    } elseif (strpos($user_agent, 'Chrome') !== false) {
        return 'Chrome';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        return 'Safari';
    } elseif (strpos($user_agent, 'Edge') !== false) {
        return 'Edge';
    } else {
        return 'Autre';
    }
}

function is_mobile() {
    return preg_match("/(android|iphone|ipad|mobile)/i", $_SERVER['HTTP_USER_AGENT']);
}