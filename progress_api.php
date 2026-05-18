<?php
// ==============================================
// API PROGRESSION DU JEU
// ==============================================

require_once 'config.php';
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Non authentifié']));
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? ($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    // Sauvegarder la progression d'un stage
    case 'save_progress':
        if ($method === 'POST') {
            save_progress();
        }
        break;
    
    // Charger la progression d'un stage
    case 'load_progress':
        if ($method === 'GET') {
            load_progress();
        }
        break;
    
    // Charger toute la progression de l'utilisateur
    case 'load_all_progress':
        if ($method === 'GET') {
            load_all_progress();
        }
        break;
    
    // Sauvegarder les réponses QCM
    case 'save_qcm':
        if ($method === 'POST') {
            save_qcm();
        }
        break;
    
    // Sauvegarder l'essai
    case 'save_essay':
        if ($method === 'POST') {
            save_essay();
        }
        break;
    
    // Compléter un stage
    case 'complete_stage':
        if ($method === 'POST') {
            complete_stage();
        }
        break;
    
    // Obtenir les récompenses totales
    case 'get_rewards':
        if ($method === 'GET') {
            get_rewards();
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
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

// ===== FONCTION: SAUVEGARDER PROGRESSION =====
function save_progress() {
    global $conn, $user_id;
    
    $data = get_request_data();
    $stage_num = intval($data['stage_num'] ?? 0);
    $current_step = intval($data['current_step'] ?? 1);
    $qcm_score = intval($data['qcm_score'] ?? 0);
    $diamonds = intval($data['diamonds'] ?? 0);
    $coins = intval($data['coins'] ?? 0);
    
    if ($stage_num < 1 || $stage_num > 10) {
        http_response_code(400);
        return echo_json(['success' => false, 'error' => 'Stage invalide']);
    }
    
    // Mettre à jour la progression
    $sql = "UPDATE progress SET 
            last_step=$current_step,
            qcm_score=$qcm_score,
            diamonds=$diamonds,
            coins=$coins,
            updated_at=NOW()
            WHERE user_id=$user_id AND stage_num=$stage_num";
    
    if ($conn->query($sql)) {
        echo_json(['success' => true, 'message' => 'Progression sauvegardée']);
    } else {
        http_response_code(500);
        echo_json(['success' => false, 'error' => 'Erreur sauvegarde: ' . $conn->error]);
    }
}

// ===== FONCTION: CHARGER PROGRESSION DE UN STAGE =====
function load_progress() {
    global $conn, $user_id;
    
    $stage_num = intval($_GET['stage_num'] ?? 0);
    
    if ($stage_num < 1 || $stage_num > 10) {
        http_response_code(400);
        return echo_json(['success' => false, 'error' => 'Stage invalide']);
    }
    
    $result = $conn->query("SELECT * FROM progress WHERE user_id=$user_id AND stage_num=$stage_num");
    $progress = $result->fetch_assoc();
    
    if ($progress) {
        // Normaliser le nom attendu par le front
        $progress['current_step'] = intval($progress['last_step'] ?? 1);
        echo_json(['success' => true, 'data' => $progress]);
    } else {
        http_response_code(404);
        echo_json(['success' => false, 'error' => 'Progression non trouvée']);
    }
}

// ===== FONCTION: CHARGER TOUTE LA PROGRESSION =====
function load_all_progress() {
    global $conn, $user_id;
    
    $result = $conn->query("SELECT * FROM progress WHERE user_id=$user_id ORDER BY stage_num");
    $progress = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['current_step'] = intval($row['last_step'] ?? 1);
        $progress[$row['stage_num']] = $row;
    }
    
    echo_json(['success' => true, 'data' => $progress]);
}

// ===== FONCTION: SAUVEGARDER RÉPONSES QCM =====
function save_qcm() {
    global $conn, $user_id;
    
    $data = get_request_data();
    $stage_num = intval($data['stage_num'] ?? 0);
    $answers = $data['answers'] ?? null;
    if (!is_array($answers)) {
        // Support FormData: q1..q5
        $answers = [];
        for ($i = 1; $i <= 5; $i++) {
            $answers["q$i"] = strval($data["q$i"] ?? $data["q{$i}_answer"] ?? '');
        }
    }
    
    if ($stage_num < 1 || $stage_num > 10) {
        http_response_code(400);
        return echo_json(['success' => false, 'error' => 'Stage invalide']);
    }
    
    // Préparer les réponses et vérifications
    $q_data = [];
    $correct_answers = get_correct_answers($stage_num);
    
    for ($i = 1; $i <= 5; $i++) {
        $answer = $answers["q$i"] ?? '';
        $is_correct = ($answer === $correct_answers["q$i"]);
        $q_data["q${i}_answer"] = $answer;
        $q_data["q${i}_correct"] = $is_correct ? 1 : 0;
    }

    $sql = "INSERT INTO qcm_answers (
                user_id, stage_num,
                q1_answer, q2_answer, q3_answer, q4_answer, q5_answer,
                q1_correct, q2_correct, q3_correct, q4_correct, q5_correct
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            ) ON DUPLICATE KEY UPDATE
                q1_answer=VALUES(q1_answer),
                q2_answer=VALUES(q2_answer),
                q3_answer=VALUES(q3_answer),
                q4_answer=VALUES(q4_answer),
                q5_answer=VALUES(q5_answer),
                q1_correct=VALUES(q1_correct),
                q2_correct=VALUES(q2_correct),
                q3_correct=VALUES(q3_correct),
                q4_correct=VALUES(q4_correct),
                q5_correct=VALUES(q5_correct)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'iisssssiiiii',
            $user_id,
            $stage_num,
            $q_data['q1_answer'],
            $q_data['q2_answer'],
            $q_data['q3_answer'],
            $q_data['q4_answer'],
            $q_data['q5_answer'],
            $q_data['q1_correct'],
            $q_data['q2_correct'],
            $q_data['q3_correct'],
            $q_data['q4_correct'],
            $q_data['q5_correct']
        );
        $ok = $stmt->execute();
        $stmt->close();
    } else {
        $ok = false;
    }

    if ($ok) {
        // Calculer le score
        $correct = 0;
        for ($i = 1; $i <= 5; $i++) {
            if (intval($q_data["q${i}_correct"]) === 1) $correct++;
        }
        $score = round(($correct / 5) * 100);

        // Optionnel: stocker le score aussi dans progress
        $conn->query("UPDATE progress SET qcm_score=$score, updated_at=NOW() WHERE user_id=$user_id AND stage_num=$stage_num");

        echo_json(['success' => true, 'message' => 'QCM sauvegardé', 'score' => $score]);
    } else {
        http_response_code(500);
        echo_json(['success' => false, 'error' => 'Erreur sauvegarde QCM: ' . $conn->error]);
    }
}

// ===== FONCTION: SAUVEGARDER ESSAI =====
function save_essay() {
    global $conn, $user_id;
    
    $data = get_request_data();
    $stage_num = intval($data['stage_num'] ?? 0);
    $content = $conn->real_escape_string($data['content'] ?? '');
    $word_count = intval($data['word_count'] ?? 0);
    
    if ($stage_num < 1 || $stage_num > 10) {
        http_response_code(400);
        return echo_json(['success' => false, 'error' => 'Stage invalide']);
    }
    
    // Calculer le score basé sur le nombre de mots
    $score = min(100, max(50, $word_count * 2));
    
    $sql = "INSERT INTO essays (user_id, stage_num, content, word_count, score)
            VALUES ($user_id, $stage_num, '$content', $word_count, $score)
            ON DUPLICATE KEY UPDATE 
            content='$content',
            word_count=$word_count,
            score=$score";
    
    if ($conn->query($sql)) {
        echo_json(['success' => true, 'message' => 'Essai sauvegardé', 'score' => $score]);
    } else {
        http_response_code(500);
        echo_json(['success' => false, 'error' => 'Erreur sauvegarde essai: ' . $conn->error]);
    }
}

// ===== FONCTION: COMPLÉTER UN STAGE =====
function complete_stage() {
    global $conn, $user_id;
    
    $data = get_request_data();
    $stage_num = intval($data['stage_num'] ?? 0);
    $diamonds = intval($data['diamonds'] ?? 0);
    $coins = intval($data['coins'] ?? 0);
    
    if ($stage_num < 1 || $stage_num > 10) {
        http_response_code(400);
        return echo_json(['success' => false, 'error' => 'Stage invalide']);
    }
    
    // Marquer le stage comme complété
    $sql = "UPDATE progress SET 
            completed=TRUE,
            diamonds=$diamonds,
            coins=$coins,
            updated_at=NOW()
            WHERE user_id=$user_id AND stage_num=$stage_num";
    
    if ($conn->query($sql)) {
        // Déverrouiller le stage suivant s'il existe
        if ($stage_num < 10) {
            $next_stage = $stage_num + 1;
            $init_next = "UPDATE progress SET last_step=1 WHERE user_id=$user_id AND stage_num=$next_stage";
            $conn->query($init_next);
        }
        
        // Mettre à jour les récompenses totales
        $result = $conn->query("SELECT SUM(diamonds) as total_diamonds, SUM(coins) as total_coins, COUNT(*) as total_completed 
                              FROM progress WHERE user_id=$user_id AND completed=TRUE");
        $stats = $result->fetch_assoc();
        
        $sql_rewards = "UPDATE rewards SET 
                       total_diamonds=" . intval($stats['total_diamonds']) . ",
                       total_coins=" . intval($stats['total_coins']) . ",
                       total_stages_completed=" . intval($stats['total_completed']) . ",
                       updated_at=NOW()
                       WHERE user_id=$user_id";
        
        $conn->query($sql_rewards);
        
        echo_json(['success' => true, 'message' => 'Stage complété', 'next_stage' => ($stage_num < 10 ? $stage_num + 1 : null)]);
    } else {
        http_response_code(500);
        echo_json(['success' => false, 'error' => 'Erreur: ' . $conn->error]);
    }
}

// ===== FONCTION: OBTENIR RÉCOMPENSES TOTALES =====
function get_rewards() {
    global $conn, $user_id;
    
    $result = $conn->query("SELECT * FROM rewards WHERE user_id=$user_id");
    $rewards = $result->fetch_assoc();
    
    echo_json(['success' => true, 'data' => $rewards]);
}

// ===== FONCTION: OBTENIR LES BONNES RÉPONSES =====
function get_correct_answers($stage_num) {
    $answers = [
        1 => ['q1' => 'ب', 'q2' => 'ب', 'q3' => 'ب', 'q4' => 'ب', 'q5' => 'ج'],
        2 => ['q1' => 'ب', 'q2' => 'ج', 'q3' => 'ج', 'q4' => 'ج', 'q5' => 'ج'],
        3 => ['q1' => 'ب', 'q2' => 'ب', 'q3' => 'ب', 'q4' => 'ج', 'q5' => 'ب'],
        4 => ['q1' => 'أ', 'q2' => 'ب', 'q3' => 'ب', 'q4' => 'ب', 'q5' => 'أ'],
        5 => ['q1' => 'ب', 'q2' => 'ب', 'q3' => 'ب', 'q4' => 'ج', 'q5' => 'ج'],
        6 => ['q1' => 'ب', 'q2' => 'ج', 'q3' => 'ب', 'q4' => 'ج', 'q5' => 'أ'],
        7 => ['q1' => 'ب', 'q2' => 'أ', 'q3' => 'ج', 'q4' => 'ج', 'q5' => 'ب'],
        8 => ['q1' => 'ب', 'q2' => 'ب', 'q3' => 'ب', 'q4' => 'ب', 'q5' => 'ج'],
        9 => ['q1' => 'ب', 'q2' => 'ج', 'q3' => 'ب', 'q4' => 'ب', 'q5' => 'ج'],
        10 => ['q1' => 'ب', 'q2' => 'ب', 'q3' => 'أ', 'q4' => 'ج', 'q5' => 'أ']
    ];
    
    return $answers[$stage_num] ?? [];
}

// ===== FONCTION UTILITAIRE =====
function echo_json($data) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

?>
