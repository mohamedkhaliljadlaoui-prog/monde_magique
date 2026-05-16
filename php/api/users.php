<?php
require_once '../php/config.php';

/**
 * API Gestion des utilisateurs
 * POST /api/users/create
 * POST /api/users/login
 * GET /api/users/{id}
 * POST /api/users/{id}/update-resources
 */

$request_method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = array_filter(explode('/', $path));

// Extraire action et parametres
$last_parts = array_slice($path_parts, -3);
$action = $last_parts[1] ?? '';
$id_user = $last_parts[2] ?? '';

if ($request_method === 'POST' && $action === 'create') {
    // Créer nouvel utilisateur
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['nom']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'nom et email requis']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('
            INSERT INTO utilisateurs (nom, email, password_hash, diamants, pieces)
            VALUES (:nom, :email, :password, :diamants, :pieces)
        ');
        
        $stmt->execute([
            ':nom' => $data['nom'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'] ?? 'default', PASSWORD_BCRYPT),
            ':diamants' => 10,
            ':pieces' => 0
        ]);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Utilisateur créé avec succès'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($request_method === 'GET' && $id_user) {
    // Récupérer données utilisateur
    try {
        $stmt = $pdo->prepare('SELECT id, nom, email, diamants, pieces, progression FROM utilisateurs WHERE id = :id');
        $stmt->execute([':id' => $id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé']);
            exit;
        }
        
        echo json_encode($user);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($request_method === 'POST' && strpos($path, 'update-resources') !== false) {
    // Mettre à jour diamants/pièces
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $stmt = $pdo->prepare('
            UPDATE utilisateurs 
            SET diamants = diamants + :diamants, pieces = pieces + :pieces
            WHERE id = :id
        ');
        
        $stmt->execute([
            ':diamants' => intval($data['diamants'] ?? 0),
            ':pieces' => intval($data['pieces'] ?? 0),
            ':id' => $id_user
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Ressources mises à jour']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint non trouvé']);
?>
