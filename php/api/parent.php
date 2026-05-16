<?php
// ===================================
// PARENT.PHP - Parent Dashboard API
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = [];
}
$action = $data['action'] ?? $_GET['action'] ?? '';
$userId = $data['user_id'] ?? $_GET['user_id'] ?? '';

try {
    switch ($action) {
        case 'parentLogin':
            $parentEmail = trim($data['email'] ?? $_GET['email'] ?? '');
            $password = $data['password'] ?? $_GET['password'] ?? '';

            if (!$parentEmail || !$password) {
                $response['message'] = 'Parent email and child password required';
                break;
            }

            $colStmt = $db->query("SHOW COLUMNS FROM users LIKE 'parent_email'");
            if ($colStmt->rowCount() === 0) {
                $response['message'] = 'parent_email column missing';
                break;
            }

            $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
            $passwordColumn = 'password';
            while ($row = $stmt->fetch()) {
                if ($row['Field'] === 'password_hash') {
                    $passwordColumn = 'password_hash';
                    break;
                }
            }

            $stmt = $db->prepare("SELECT id, username, {$passwordColumn} as password FROM users WHERE parent_email = ?");
            $stmt->execute([$parentEmail]);
            $matches = false;
            $matchedChild = null;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    $matches = true;
                    $matchedChild = [
                        'id' => (int)$row['id'],
                        'username' => $row['username']
                    ];
                    break;
                }
            }

            if ($matches) {
                $response['success'] = true;
                $response['message'] = 'Parent login ok';
                $response['child'] = $matchedChild;
            } else {
                $response['message'] = 'Identifiants incorrects';
            }
            break;
        case 'getChildProgress':
            if (!$userId) {
                $response['message'] = 'User ID required';
                break;
            }

            // Get user info
            $user = new User($db);
            $user->id = $userId;
            $user->readById();

            // Get progress statistics
            $stmt = $db->prepare("
                SELECT 
                    stage_id,
                    COUNT(*) as total_stations,
                    SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_stations,
                    AVG(score) as avg_score,
                    SUM(time_spent) as total_time
                FROM user_progress
                WHERE user_id = ?
                GROUP BY stage_id
            ");
            $stmt->execute([$userId]);
            $stageProgress = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get quiz statistics
            $stmt = $db->prepare("
                SELECT 
                    AVG(score) as avg_quiz_score,
                    COUNT(*) as total_quizzes,
                    SUM(time_spent) as total_quiz_time
                FROM quiz_results
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $quizStats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get time spent per day (last 7 days)
            $stmt = $db->prepare("
                SELECT 
                    DATE(completed_at) as date,
                    SUM(time_spent) as time_spent
                FROM user_progress
                WHERE user_id = ? AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(completed_at)
                ORDER BY date
            ");
            $stmt->execute([$userId]);
            $dailyTime = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = [
                'user' => [
                    'username' => $user->username,
                    'guide_name' => $user->guide_name,
                    'level' => $user->level,
                    'xp' => $user->xp
                ],
                'stage_progress' => $stageProgress,
                'quiz_stats' => $quizStats,
                'daily_time' => $dailyTime
            ];
            break;

        case 'getActivityLog':
            $limit = $data['limit'] ?? 20;

            $stmt = $db->prepare("
                SELECT 'progress' as type, stage_id, station_id, score, completed_at as timestamp
                FROM user_progress
                WHERE user_id = ?
                UNION ALL
                SELECT 'quiz' as type, stage_id, NULL as station_id, score, completed_at as timestamp
                FROM quiz_results
                WHERE user_id = ?
                ORDER BY timestamp DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $userId, $limit]);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $activities;
            break;

        case 'getChildren':
            $parentEmail = $data['email'] ?? $_GET['email'] ?? '';
            if (!$parentEmail) {
                $response['message'] = 'Parent email required';
                break;
            }

            $colStmt = $db->query("SHOW COLUMNS FROM users LIKE 'parent_email'");
            if ($colStmt->rowCount() === 0) {
                $response['message'] = 'parent_email column missing';
                break;
            }

            $stmt = $db->prepare("SELECT id, username, level, xp, coins, diamonds, current_stage FROM users WHERE parent_email = ? ORDER BY created_at DESC");
            $stmt->execute([$parentEmail]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['children'] = $children;
            break;

        case 'getStrengthsWeaknesses':
            // Analyze quiz performance by stage
            $stmt = $db->prepare("
                SELECT 
                    stage_id,
                    AVG(score) as avg_score,
                    COUNT(*) as attempts
                FROM quiz_results
                WHERE user_id = ?
                GROUP BY stage_id
                ORDER BY avg_score DESC
            ");
            $stmt->execute([$userId]);
            $performance = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $strengths = [];
            $weaknesses = [];

            foreach ($performance as $stage) {
                if ($stage['avg_score'] >= 80) {
                    $strengths[] = $stage;
                } elseif ($stage['avg_score'] < 60) {
                    $weaknesses[] = $stage;
                }
            }

            $response['success'] = true;
            $response['data'] = [
                'strengths' => $strengths,
                'weaknesses' => $weaknesses
            ];
            break;

        case 'setTimeLimit':
            $dailyLimit = $data['daily_limit'] ?? 3600; // seconds

            $stmt = $db->prepare("
                INSERT INTO user_settings (user_id, setting_key, setting_value)
                VALUES (?, 'daily_time_limit', ?)
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$userId, $dailyLimit, $dailyLimit]);

            $response['success'] = true;
            $response['message'] = 'Time limit updated';
            break;

        case 'getWeeklyReport':
            $stmt = $db->prepare("
                SELECT 
                    DATE(completed_at) as date,
                    COUNT(DISTINCT stage_id) as stages_worked,
                    SUM(time_spent) as time_spent,
                    AVG(score) as avg_score
                FROM user_progress
                WHERE user_id = ? AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(completed_at)
            ");
            $stmt->execute([$userId]);
            $weeklyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $weeklyData;
            break;

        default:
            $response['message'] = 'Invalid action';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
