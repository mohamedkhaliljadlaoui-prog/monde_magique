<?php
// ===================================
// PROFILE.PHP - Profile Management API
// Gestion des modifications de profil utilisateur
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? $_GET['action'] ?? '';
$token = $data['token'] ?? $_GET['token'] ?? '';

// Verify token and get user ID
function getUserIdFromToken($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['user_id'] ?? null;
}

$userId = getUserIdFromToken($token);

if (!$userId) {
    $response['message'] = 'Token invalide ou manquant';
    echo json_encode($response);
    exit;
}

try {
    switch ($action) {
        case 'updateProfile':
            // Mise à jour d'un champ du profil
            $field = $data['field'] ?? '';
            $value = $data['value'] ?? '';

            if (empty($field) || empty($value)) {
                $response['message'] = 'Champ et valeur requis';
                break;
            }

            // Liste des champs autorisés
            $allowedFields = ['username', 'email', 'guide_name', 'birth_date', 'gender'];
            if (!in_array($field, $allowedFields)) {
                $response['message'] = 'Champ non autorisé';
                break;
            }

            // Validations spécifiques par champ
            switch ($field) {
                case 'username':
                    // Validation username: 3-50 caractères, alphanumerique + underscore
                    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $value)) {
                        $response['message'] = 'Nom d\'utilisateur invalide (3-50 caractères, alphanumerique et underscore)';
                        break 2;
                    }

                    // Vérifier l'unicité
                    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                    $stmt->execute([$value, $userId]);
                    if ($stmt->fetch()) {
                        $response['message'] = 'Ce nom d\'utilisateur est déjà pris';
                        break 2;
                    }
                    break;

                case 'email':
                    // Validation email
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $response['message'] = 'Adresse email invalide';
                        break 2;
                    }

                    // Vérifier l'unicité
                    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$value, $userId]);
                    if ($stmt->fetch()) {
                        $response['message'] = 'Cette adresse email est déjà utilisée';
                        break 2;
                    }
                    break;

                case 'guide_name':
                    // Validation guide_name: 1-50 caractères
                    if (strlen($value) < 1 || strlen($value) > 50) {
                        $response['message'] = 'Nom du guide invalide (1-50 caractères)';
                        break 2;
                    }
                    break;

                case 'birth_date':
                    // Validation date de naissance
                    $date = DateTime::createFromFormat('Y-m-d', $value);
                    if (!$date) {
                        $response['message'] = 'Format de date invalide (YYYY-MM-DD)';
                        break 2;
                    }

                    // Calculer l'âge
                    $now = new DateTime();
                    $age = $now->diff($date)->y;

                    // Âge entre 5 et 18 ans
                    if ($age < 5 || $age > 18) {
                        $response['message'] = 'L\'âge doit être entre 5 et 18 ans';
                        break 2;
                    }
                    break;

                case 'gender':
                    // Validation genre
                    if (!in_array($value, ['boy', 'girl'])) {
                        $response['message'] = 'Genre invalide (boy ou girl)';
                        break 2;
                    }
                    break;
            }

            // Mise à jour du champ
            $stmt = $db->prepare("UPDATE users SET $field = ?, profile_updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$value, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Profil mis à jour avec succès';
                $response['field'] = $field;
                $response['value'] = $value;
            } else {
                $response['message'] = 'Erreur lors de la mise à jour';
            }
            break;

        case 'updatePassword':
            // Changer le mot de passe
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                $response['message'] = 'Mot de passe actuel et nouveau mot de passe requis';
                break;
            }

            // Valider la force du nouveau mot de passe
            if (strlen($newPassword) < 8) {
                $response['message'] = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
                break;
            }

            if (!preg_match('/[A-Z]/', $newPassword)) {
                $response['message'] = 'Le nouveau mot de passe doit contenir au moins une majuscule';
                break;
            }

            if (!preg_match('/[0-9]/', $newPassword)) {
                $response['message'] = 'Le nouveau mot de passe doit contenir au moins un chiffre';
                break;
            }

            // Récupérer le mot de passe actuel
            $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $response['message'] = 'Utilisateur non trouvé';
                break;
            }

            // Vérifier le mot de passe actuel
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $response['message'] = 'Mot de passe actuel incorrect';
                break;
            }

            // Hacher le nouveau mot de passe
            $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour
            $stmt = $db->prepare("UPDATE users SET password_hash = ?, profile_updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$newPasswordHash, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Mot de passe mis à jour avec succès';
            } else {
                $response['message'] = 'Erreur lors de la mise à jour du mot de passe';
            }
            break;

        case 'updateAvatar':
            // Changer l'avatar
            $avatarUrl = $data['avatar_url'] ?? '';

            if (empty($avatarUrl)) {
                $response['message'] = 'URL de l\'avatar requise';
                break;
            }

            // Validation: l'avatar doit être dans assets/ ou une URL DiceBear
            $isLocal = strpos($avatarUrl, 'assets/images/avatars/') === 0;
            $isDiceBear = strpos($avatarUrl, 'https://api.dicebear.com/') === 0;
            $isMultiAvatar = strpos($avatarUrl, 'https://api.multiavatar.com/') === 0;

            if (!$isLocal && !$isDiceBear && !$isMultiAvatar) {
                $response['message'] = 'URL d\'avatar non autorisée';
                break;
            }

            // Mise à jour
            $stmt = $db->prepare("UPDATE users SET avatar_url = ?, profile_updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$avatarUrl, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Avatar mis à jour avec succès';
                $response['avatar_url'] = $avatarUrl;
            } else {
                $response['message'] = 'Erreur lors de la mise à jour de l\'avatar';
            }
            break;

        case 'getProfileData':
            // Récupérer les données du profil (peut être pour soi-même ou un autre utilisateur)
            $targetUserId = $data['user_id'] ?? $_GET['user_id'] ?? $userId;

            // Récupérer les paramètres de confidentialité
            $stmt = $db->prepare("SELECT profile_visibility FROM users WHERE id = ?");
            $stmt->execute([$targetUserId]);
            $privacySettings = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$privacySettings) {
                $response['message'] = 'Utilisateur non trouvé';
                break;
            }

            // Vérifier les permissions
            $canView = false;
            if ($targetUserId == $userId) {
                // L'utilisateur peut toujours voir son propre profil
                $canView = true;
            } elseif ($privacySettings['profile_visibility'] == 'public') {
                $canView = true;
            } elseif ($privacySettings['profile_visibility'] == 'friends_only') {
                // Vérifier s'ils sont amis
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM friends WHERE user_id = ? AND friend_id = ? AND friendship_status = 'active'");
                $stmt->execute([$userId, $targetUserId]);
                $friendCheck = $stmt->fetch(PDO::FETCH_ASSOC);
                $canView = $friendCheck['total'] > 0;
            }

            if (!$canView) {
                $response['message'] = 'Vous n\'avez pas la permission de voir ce profil';
                break;
            }

            // Récupérer les données du profil
            $stmt = $db->prepare("
                SELECT
                    id, username, guide_name, avatar_url, level, xp,
                    coins, diamonds, current_stage, created_at, last_login
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$targetUserId]);
            $profileData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profileData) {
                // Récupérer les statistiques
                $stmt = $db->prepare("
                    SELECT
                        COUNT(DISTINCT stage_id) as stages_completed,
                        SUM(score) as total_score
                    FROM stage_progress
                    WHERE user_id = ? AND completed = 1
                ");
                $stmt->execute([$targetUserId]);
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);

                $response['success'] = true;
                $response['data'] = array_merge($profileData, [
                    'stages_completed' => $stats['stages_completed'] ?? 0,
                    'total_score' => $stats['total_score'] ?? 0
                ]);
            } else {
                $response['message'] = 'Profil non trouvé';
            }
            break;

        case 'updatePrivacySettings':
            // Mettre à jour les paramètres de confidentialité
            $visibility = $data['profile_visibility'] ?? '';

            if (!in_array($visibility, ['public', 'friends_only', 'private'])) {
                $response['message'] = 'Paramètre de confidentialité invalide';
                break;
            }

            $stmt = $db->prepare("UPDATE users SET profile_visibility = ? WHERE id = ?");
            if ($stmt->execute([$visibility, $userId])) {
                $response['success'] = true;
                $response['message'] = 'Paramètres de confidentialité mis à jour';
                $response['profile_visibility'] = $visibility;
            } else {
                $response['message'] = 'Erreur lors de la mise à jour';
            }
            break;

        default:
            $response['message'] = 'Action non reconnue';
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Erreur serveur: ' . $e->getMessage();
}

echo json_encode($response);
?>
