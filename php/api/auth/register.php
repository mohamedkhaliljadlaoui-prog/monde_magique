<?php
// register.php - Inscription des utilisateurs

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once '../../config/database.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Validation basique
$required = ['username','email','password','gender','birth_date','language'];
foreach ($required as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Champ requis manquant: $field"]);
        exit;
    }
}

try {
    $db = Database::getInstance()->getConnection();

    // Vérifier unicité username / email
    $stmt = $db->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
    $stmt->execute([':u' => $data['username'], ':e' => $data['email']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur ou email déjà utilisé']);
        exit;
    }

    // Calculer l\'âge à partir de birth_date
    $birth = new DateTime($data['birth_date']);
    $now = new DateTime();
    $age = (int)$now->format('Y') - (int)$birth->format('Y');

    // Hacher le mot de passe
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

    // Valeurs par défaut
    $coins = 0; $diamonds = 3; $xp = 0; $level = 1; $current_stage = 1;

    // Détecter les colonnes disponibles dans la table users
    $stmt = $db->query("SHOW COLUMNS FROM users");
    $availableColumns = [];
    while ($row = $stmt->fetch()) {
        $availableColumns[] = $row['Field'];
    }
    
    error_log('Colonnes disponibles: ' . implode(', ', $availableColumns));

    // Déterminer les colonnes à utiliser
    $passwordCol = in_array('password_hash', $availableColumns) ? 'password_hash' : 'password';
    $genderCol = in_array('guide_gender', $availableColumns) ? 'guide_gender' : 'gender';
    $hasAge = in_array('age', $availableColumns);
    $hasGuideName = in_array('guide_name', $availableColumns);
    $hasLanguage = in_array('language', $availableColumns);
    $hasParentEmail = in_array('parent_email', $availableColumns);
    
    // Construire la requête SQL dynamiquement
    $columns = ['username', 'email', $passwordCol];
    $values = [':username', ':email', ':password'];
    $params = [
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':password' => $passwordHash
    ];
    
    // Ajouter gender
    $columns[] = $genderCol;
    $values[] = ':gender';
    $params[':gender'] = $data['gender'];
    
    // Ajouter les colonnes optionnelles
    if ($hasAge) {
        $columns[] = 'age';
        $values[] = ':age';
        $params[':age'] = $age;
    }
    
    if ($hasGuideName) {
        $columns[] = 'guide_name';
        $values[] = ':guide_name';
        $params[':guide_name'] = $data['guide_name'] ?? ($data['gender'] === 'boy' ? 'تيو' : 'ليا');
    }
    
    $columns[] = 'level';
    $values[] = ':level';
    $params[':level'] = $level;
    
    $columns[] = 'xp';
    $values[] = ':xp';
    $params[':xp'] = $xp;
    
    $columns[] = 'coins';
    $values[] = ':coins';
    $params[':coins'] = $coins;
    
    $columns[] = 'diamonds';
    $values[] = ':diamonds';
    $params[':diamonds'] = $diamonds;
    
    $columns[] = 'current_stage';
    $values[] = ':current_stage';
    $params[':current_stage'] = $current_stage;
    
    if ($hasLanguage) {
        $columns[] = 'language';
        $values[] = ':language';
        $params[':language'] = $data['language'];
    }

    if ($hasParentEmail) {
        $parentEmail = isset($data['parent_email']) ? trim($data['parent_email']) : '';
        if ($parentEmail !== '') {
            $columns[] = 'parent_email';
            $values[] = ':parent_email';
            $params[':parent_email'] = $parentEmail;
        }
    }
    
    $columns[] = 'created_at';
    $values[] = 'NOW()';

    // Construire et exécuter la requête
    $sql = "INSERT INTO users (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ")";
    error_log('SQL: ' . $sql);
    error_log('Params: ' . json_encode($params));
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $userId = $db->lastInsertId();

    // Réponse
    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie',
        'token' => 'token_' . bin2hex(random_bytes(8)),
        'user' => [
            'id' => (int)$userId,
            'username' => $data['username'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'guide_name' => $data['guide_name'] ?? null,
            'level' => $level,
            'xp' => $xp,
            'coins' => $coins,
            'diamonds' => $diamonds,
            'current_stage' => $current_stage,
            'language' => $data['language'],
            'parent_email' => $data['parent_email'] ?? null
        ]
    ]);

} catch (PDOException $e) {
    error_log('Erreur PDO register: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('Erreur register: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
