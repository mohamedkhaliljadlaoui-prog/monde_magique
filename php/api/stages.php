<?php
require_once '../php/config.php';

/**
 * API Gestion des stages
 * POST /api/stages/complete
 * GET /api/stages/user/{id_user}
 * POST /api/stages/{id_user}/{num_stage}/complete-etape
 */

$request_method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = array_filter(explode('/', $path));

$action = $path_parts[array_key_last($path_parts) - 2] ?? '';
$id_user = $path_parts[array_key_last($path_parts) - 1] ?? '';
$num_stage = $path_parts[array_key_last($path_parts)] ?? '';

// Récupérer progression d'un utilisateur
if ($request_method === 'GET' && $action === 'user') {
    try {
        // Tous les stages de l'utilisateur
        $stmt = $pdo->prepare('
            SELECT num_stage, status, score, diamants_gagnes, pieces_gagnees 
            FROM stages_completion 
            WHERE id_utilisateur = :id
        ');
        $stmt->execute([':id' => $id_user]);
        $stages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['stages' => $stages]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Compléter étape d'un stage
if ($request_method === 'POST' && strpos($path, 'complete-etape') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $num_etape = $data['num_etape'] ?? 0;
    
    try {
        // Créer/mettre à jour entrée étape
        $stmt = $pdo->prepare('
            INSERT INTO etapes_completion (id_utilisateur, num_stage, num_etape, status)
            VALUES (:id_user, :num_stage, :num_etape, "completee")
            ON DUPLICATE KEY UPDATE status = "completee", date_completion = NOW()
        ');
        
        $stmt->execute([
            ':id_user' => $id_user,
            ':num_stage' => $num_stage,
            ':num_etape' => $num_etape
        ]);
        
        // Vérifier si toutes les étapes sont faites
        $check = $pdo->prepare('
            SELECT COUNT(*) as total FROM etapes_completion
            WHERE id_utilisateur = :id_user AND num_stage = :num_stage AND status = "completee"
        ');
        $check->execute([':id_user' => $id_user, ':num_stage' => $num_stage]);
        $total = $check->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($total >= 5) {
            // Toutes les étapes faites: marquer stage comme terminé + reward
            $reward_stmt = $pdo->prepare('
                INSERT INTO stages_completion (id_utilisateur, num_stage, status, score, diamants_gagnes, pieces_gagnees)
                VALUES (:id_user, :num_stage, "termine", :score, :diamants, :pieces)
                ON DUPLICATE KEY UPDATE status = "termine", score = :score, diamants_gagnes = :diamants, pieces_gagnees = :pieces
            ');
            
            $diamants_reward = 30 + ($num_stage * 5);
            $pieces_reward = $diamants_reward * 5;
            
            $reward_stmt->execute([
                ':id_user' => $id_user,
                ':num_stage' => $num_stage,
                ':score' => 100,
                ':diamants' => $diamants_reward,
                ':pieces' => $pieces_reward
            ]);
            
            // Ajouter ressources utilisateur
            $user_stmt = $pdo->prepare('
                UPDATE utilisateurs 
                SET diamants = diamants + :diamants, pieces = pieces + :pieces, progression = progression + 1
                WHERE id = :id
            ');
            $user_stmt->execute([
                ':diamants' => $diamants_reward,
                ':pieces' => $pieces_reward,
                ':id' => $id_user
            ]);
            
            echo json_encode([
                'success' => true,
                'stage_complete' => true,
                'diamants_gagnes' => $diamants_reward,
                'pieces_gagnees' => $pieces_reward,
                'message' => 'Stage terminé! 🎉'
            ]);
        } else {
            echo json_encode(['success' => true, 'stage_complete' => false]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint non trouvé']);
?>
