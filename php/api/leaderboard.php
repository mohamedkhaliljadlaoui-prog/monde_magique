<?php
// ===================================
// LEADERBOARD.PHP - Leaderboard API
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getTopPlayers':
            $limit = $_GET['limit'] ?? 10;
            
            $query = "SELECT 
                        u.id,
                        u.username,
                        u.guide_name,
                        u.guide_gender,
                        u.level,
                        u.xp,
                        u.coins,
                        COUNT(DISTINCT up.stage_id) as stages_completed
                      FROM users u
                      LEFT JOIN user_progress up ON u.id = up.user_id AND up.completed = 1
                      GROUP BY u.id
                      ORDER BY u.level DESC, u.xp DESC
                      LIMIT ?";

            $stmt = $db->prepare($query);
            $stmt->execute([$limit]);
            $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $leaderboard;
            break;

        case 'getStageLeaderboard':
            $stageId = $_GET['stage_id'] ?? 1;
            $limit = $_GET['limit'] ?? 10;

            $query = "SELECT 
                        u.username,
                        u.guide_name,
                        qr.score,
                        qr.time_spent,
                        qr.completed_at
                      FROM quiz_results qr
                      JOIN users u ON qr.user_id = u.id
                      WHERE qr.stage_id = ?
                      ORDER BY qr.score DESC, qr.time_spent ASC
                      LIMIT ?";

            $stmt = $db->prepare($query);
            $stmt->execute([$stageId, $limit]);
            $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $leaderboard;
            break;

        case 'getUserRank':
            $userId = $_GET['user_id'] ?? 0;

            $query = "SELECT COUNT(*) + 1 as rank
                      FROM users
                      WHERE level > (SELECT level FROM users WHERE id = ?)
                      OR (level = (SELECT level FROM users WHERE id = ?) 
                          AND xp > (SELECT xp FROM users WHERE id = ?))";

            $stmt = $db->prepare($query);
            $stmt->execute([$userId, $userId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = ['rank' => $result['rank']];
            break;

        default:
            $response['message'] = 'Invalid action';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
