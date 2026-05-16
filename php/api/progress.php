<?php
// ===================================
// PROGRESS.PHP - Progress API
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Progress.php';

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? $_GET['action'] ?? '';
$token = $data['token'] ?? $_GET['token'] ?? '';

// Verify token and get user ID
function getUserIdFromToken($token) {
    // Simple JWT decode (in production, use proper JWT library)
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    
    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['user_id'] ?? null;
}

$userId = getUserIdFromToken($token);

if (!$userId && $action !== 'test') {
    $response['message'] = 'Invalid token';
    echo json_encode($response);
    exit;
}

try {
    switch ($action) {
        case 'getUserData':
            $user = new User($db);
            $user->id = $userId;
            
            if ($user->readById()) {
                $response['success'] = true;
                $response['data'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'guide_name' => $user->guide_name,
                    'guide_gender' => $user->guide_gender,
                    'level' => $user->level,
                    'xp' => $user->xp,
                    'coins' => $user->coins,
                    'diamonds' => $user->diamonds,
                    'current_stage' => $user->current_stage
                ];
            }
            break;

        case 'getUserStats':
            $user = new User($db);
            $user->id = $userId;
            $user->readById();

            // Get additional stats
            $stmt = $db->prepare("
                SELECT 
                    COUNT(DISTINCT stage_id) as stages_completed,
                    COUNT(*) as quizzes_completed
                FROM user_progress 
                WHERE user_id = ? AND completed = 1
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("
                SELECT COUNT(*) as questions 
                FROM chatbot_logs 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $chatbotStats = $stmt->fetch(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = [
                'guide_name' => $user->guide_name,
                'level' => $user->level,
                'xp' => $user->xp,
                'coins' => $user->coins,
                'diamonds' => $user->diamonds,
                'stages_completed' => $stats['stages_completed'],
                'quizzes_completed' => $stats['quizzes_completed'],
                'chatbot_questions' => $chatbotStats['questions']
            ];
            break;

        case 'saveProgress':
            $progress = new Progress($db);
            $progress->user_id = $userId;
            $progress->stage_id = $data['stage_id'];
            $progress->station_id = $data['station_id'];
            $progress->completed = $data['completed'] ?? 1;
            $progress->score = $data['score'] ?? 0;
            $progress->time_spent = $data['time_spent'] ?? 0;

            if ($progress->saveProgress()) {
                // Update user XP and coins
                $user = new User($db);
                $user->id = $userId;
                $user->readById();

                $rewards = calculateRewards($data['station_id'], $data['score']);
                $levelUpResult = $user->addXP($rewards['xp']);
                $user->addCoins($rewards['coins']);
                if ($rewards['diamonds'] > 0) {
                    $user->addDiamonds($rewards['diamonds']);
                }
                $user->update();

                $response['success'] = true;
                $response['rewards'] = $rewards;
                $response['levelUp'] = $levelUpResult['levelUp'];
                if ($levelUpResult['levelUp']) {
                    $response['newLevel'] = $levelUpResult['newLevel'];
                }
            }
            break;

        case 'getStageProgress':
            $progress = new Progress($db);
            $progress->user_id = $userId;
            $progress->stage_id = $data['stage_id'] ?? $_GET['stage_id'];

            $stageProgress = $progress->getStageProgress();
            $percentage = $progress->getCompletionPercentage();

            $response['success'] = true;
            $response['data'] = [
                'progress' => $stageProgress,
                'percentage' => $percentage,
                'completed' => $progress->isStageCompleted()
            ];
            break;

        case 'getAchievements':
            $stmt = $db->prepare("
                SELECT achievement_id 
                FROM user_achievements 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            
            $achievements = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $achievements[] = $row['achievement_id'];
            }

            $response['success'] = true;
            $response['data'] = $achievements;
            break;

        case 'getRecentActivity':
            $stmt = $db->prepare("
                SELECT 
                    'stage' as type,
                    stage_id,
                    completed_at as time,
                    score
                FROM user_progress
                WHERE user_id = ?
                ORDER BY completed_at DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $activities;
            break;

        case 'getUserEconomy':
            $user = new User($db);
            $user->id = $userId;
            $user->readById();

            $transaction = new Transaction($db);
            $transaction->user_id = $userId;
            $ownedItems = $transaction->getOwnedItems();

            $response['success'] = true;
            $response['data'] = [
                'coins' => $user->coins,
                'diamonds' => $user->diamonds,
                'level' => $user->level,
                'owned_items' => $ownedItems
            ];
            break;

        default:
            $response['message'] = 'Invalid action';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);

// Helper function
function calculateRewards($stationId, $score) {
    $rewards = [
        'xp' => 0,
        'coins' => 0,
        'diamonds' => 0
    ];

    switch ($stationId) {
        case 1: // PDF
            $rewards = ['xp' => 50, 'coins' => 10, 'diamonds' => 0];
            break;
        case 2: // Video
            $rewards = ['xp' => 75, 'coins' => 15, 'diamonds' => 0];
            break;
        case 3: // Game
            $rewards = ['xp' => 100, 'coins' => 20, 'diamonds' => 1];
            break;
        case 4: // Image Quiz
            $rewards = ['xp' => 100, 'coins' => 20, 'diamonds' => 1];
            break;
        case 5: // Final Quiz
            $rewards = ['xp' => 200, 'coins' => 50, 'diamonds' => 2];
            if ($score >= 90) {
                $rewards['diamonds'] += 3;
            }
            break;
    }

    return $rewards;
}
?>
