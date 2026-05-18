<?php
// ==============================================
// PARENT_API.PHP - Connexion parent + suivi enfant
// Basé sur la base simple (config.php: users/progress/rewards)
// ==============================================

require_once 'config.php';
session_start();

// Éviter de casser le JSON avec des warnings/notices
error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_REQUEST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

function echo_json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

function error_json($message, $status = 400, $extra = []) {
    echo_json(array_merge(['success' => false, 'error' => $message, 'message' => $message], $extra), $status);
}

function get_request_data() {
    if (!empty($_POST)) {
        return $_POST;
    }
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function require_parent_session() {
    if (empty($_SESSION['parent_email'])) {
        error_json('Non authentifié (parent)', 401);
    }
    return $_SESSION['parent_email'];
}

function get_children_by_parent_email($conn, $parentEmail) {
    $parentEmailEsc = $conn->real_escape_string($parentEmail);
    $sql = "SELECT id, username, email, parent_email, last_login, created_at FROM users WHERE parent_email='$parentEmailEsc' ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $children = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $children[] = [
                'id' => (int)$row['id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'last_login' => $row['last_login'],
                'created_at' => $row['created_at'],
            ];
        }
    }
    return $children;
}

function assert_child_belongs_to_parent($conn, $childId, $parentEmail) {
    $childId = (int)$childId;
    $parentEmailEsc = $conn->real_escape_string($parentEmail);
    $check = $conn->query("SELECT id FROM users WHERE id=$childId AND parent_email='$parentEmailEsc' LIMIT 1");
    if (!$check || $check->num_rows === 0) {
        error_json("Enfant introuvable pour ce parent", 403);
    }
    return $childId;
}

function compute_child_dashboard_data($conn, $childId) {
    $childId = (int)$childId;

    $userRow = $conn->query("SELECT id, username, last_login FROM users WHERE id=$childId LIMIT 1");
    $user = $userRow ? $userRow->fetch_assoc() : null;
    if (!$user) {
        return null;
    }

    $progressRes = $conn->query("SELECT stage_num, completed, qcm_score, coins, diamonds, last_step, updated_at FROM progress WHERE user_id=$childId ORDER BY stage_num");
    $stageProgress = [];
    $stagesCompleted = 0;
    $scoreSum = 0;
    $scoreCount = 0;
    $totalCoins = 0;
    $totalDiamonds = 0;

    $activities = [];

    if ($progressRes) {
        while ($row = $progressRes->fetch_assoc()) {
            $stageNum = (int)$row['stage_num'];
            $completed = (int)$row['completed'] === 1;
            $lastStep = (int)($row['last_step'] ?? 1);
            $qcmScore = (int)($row['qcm_score'] ?? 0);

            if ($completed) {
                $stagesCompleted++;
            }

            if ($qcmScore > 0) {
                $scoreSum += $qcmScore;
                $scoreCount++;
            }

            $totalCoins += (int)($row['coins'] ?? 0);
            $totalDiamonds += (int)($row['diamonds'] ?? 0);

            // Completion approximative sur 5 étapes (cohérent avec la plupart des stages générés)
            $completion = $completed ? 100 : max(0, min(90, ($lastStep - 1) * 20));
            $stageProgress[] = [
                'stage_num' => $stageNum,
                'completion' => $completion,
                'completed' => $completed,
                'qcm_score' => $qcmScore,
            ];

            // Activité simple basée sur updated_at
            if (!empty($row['updated_at'])) {
                $activities[] = [
                    'type' => $completed ? 'stage_complete' : 'progress',
                    'title' => $completed ? "Stage $stageNum complété" : "Progression stage $stageNum",
                    'description' => $completed ? "Stage $stageNum terminé" : "Dernière étape: $lastStep",
                    'created_at' => $row['updated_at'],
                ];
            }
        }
    }

    // Trier les activités par date desc et limiter
    usort($activities, function ($a, $b) {
        return strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? '');
    });
    $recentActivities = array_slice($activities, 0, 12);

    $averageScore = $scoreCount > 0 ? (int)round($scoreSum / $scoreCount) : 0;

    return [
        'child' => [
            'id' => (int)$user['id'],
            'username' => $user['username'],
        ],
        // Champs attendus par parent-dashboard.html
        'total_play_time' => 0,
        'daily_playtime' => 0,
        'stages_completed' => $stagesCompleted,
        'average_score' => $averageScore,
        'achievements_count' => 0,
        'chatbot_questions' => 0,
        'last_login' => $user['last_login'],
        'stage_progress' => $stageProgress,
        'recent_activities' => $recentActivities,
        // Extras utiles (sans casser l'UI)
        'total_coins' => $totalCoins,
        'total_diamonds' => $totalDiamonds,
    ];
}

// Router
switch ($action) {
    case 'parent_login': {
        if ($method !== 'POST') {
            error_json('Méthode invalide', 405);
        }
        $data = get_request_data();
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            error_json('Email et mot de passe requis', 400);
        }

        // Rechercher tous les enfants associés à cet email parent et vérifier le mot de passe de l'un d'eux
        $emailEsc = $conn->real_escape_string($email);
        $res = $conn->query("SELECT id, username, password FROM users WHERE parent_email='$emailEsc'");
        if (!$res || $res->num_rows === 0) {
            error_json('Aucun enfant lié à cet email parent', 401);
        }

        $matchedChild = null;
        while ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $matchedChild = ['id' => (int)$row['id'], 'username' => $row['username']];
                break;
            }
        }

        if (!$matchedChild) {
            error_json('Identifiants incorrects', 401);
        }

        $_SESSION['parent_email'] = $email;

        $children = get_children_by_parent_email($conn, $email);
        echo_json([
            'success' => true,
            'message' => 'Connexion parent réussie',
            'child' => $matchedChild,
            'children' => $children,
        ]);
        break;
    }

    case 'parent_logout': {
        unset($_SESSION['parent_email']);
        echo_json(['success' => true, 'message' => 'Déconnexion parent réussie']);
        break;
    }

    case 'get_children': {
        $parentEmail = require_parent_session();
        $children = get_children_by_parent_email($conn, $parentEmail);
        echo_json(['success' => true, 'children' => $children]);
        break;
    }

    case 'get_child_data': {
        $parentEmail = require_parent_session();
        $childId = $_REQUEST['childId'] ?? $_REQUEST['user_id'] ?? '';
        if (empty($childId)) {
            error_json('childId requis', 400);
        }
        $childId = assert_child_belongs_to_parent($conn, $childId, $parentEmail);
        $data = compute_child_dashboard_data($conn, $childId);
        if (!$data) {
            error_json('Données enfant introuvables', 404);
        }
        echo_json(['success' => true, 'data' => $data]);
        break;
    }

    case 'export_report': {
        require_parent_session();
        echo_json([
            'success' => false,
            'error' => "Export PDF non implémenté dans cette version"
        ], 501);
        break;
    }

    default:
        error_json('Action non reconnue', 400);
}
