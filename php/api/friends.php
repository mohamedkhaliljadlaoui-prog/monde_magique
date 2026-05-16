<?php
// ===================================
// FRIENDS.PHP - Friends System API
// Gestion complète du système d'amis
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Friend.php';
require_once '../models/FriendRequest.php';

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
        case 'searchUsers':
            // Rechercher des utilisateurs
            $query = $data['query'] ?? $_GET['query'] ?? '';
            $limit = $data['limit'] ?? $_GET['limit'] ?? 20;

            if (strlen($query) < 2) {
                $response['message'] = 'Requête de recherche trop courte (minimum 2 caractères)';
                break;
            }

            // Récupérer les IDs des amis existants
            $friend = new Friend($db);
            $friendIds = $friend->getFriendIds($userId);
            $friendIds[] = $userId; // Exclure soi-même

            $placeholders = str_repeat('?,', count($friendIds) - 1) . '?';

            $stmt = $db->prepare("
                SELECT id, username, guide_name, avatar_url, level, xp
                FROM users
                WHERE (username LIKE ? OR guide_name LIKE ?)
                  AND id NOT IN ($placeholders)
                  AND is_active = 1
                LIMIT ?
            ");

            $searchTerm = "%$query%";
            $params = array_merge([$searchTerm, $searchTerm], $friendIds, [$limit]);
            $stmt->execute($params);

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $users;
            $response['count'] = count($users);
            break;

        case 'sendFriendRequest':
            // Envoyer une demande d'ami
            $receiverId = $data['receiver_id'] ?? 0;
            $message = $data['message'] ?? '';

            if (!$receiverId) {
                $response['message'] = 'ID du destinataire requis';
                break;
            }

            // Rate limiting: vérifier le nombre de demandes aujourd'hui
            $friendRequest = new FriendRequest($db);
            $requestsToday = $friendRequest->countRequestsToday($userId);

            if ($requestsToday >= 20) {
                $response['message'] = 'Limite de 20 demandes par jour atteinte';
                break;
            }

            // Vérifier que les utilisateurs ne sont pas déjà amis
            $friend = new Friend($db);
            if ($friend->areFriends($userId, $receiverId)) {
                $response['message'] = 'Vous êtes déjà amis';
                break;
            }

            // Envoyer la demande
            $requestId = $friendRequest->sendRequest($userId, $receiverId, $message);

            if ($requestId) {
                $response['success'] = true;
                $response['message'] = 'Demande d\'ami envoyée';
                $response['request_id'] = $requestId;
            } else {
                $response['message'] = 'Impossible d\'envoyer la demande (demande déjà existante ou erreur)';
            }
            break;

        case 'getFriendRequests':
            // Obtenir les demandes d'amis
            $type = $data['type'] ?? $_GET['type'] ?? 'received';

            $friendRequest = new FriendRequest($db);

            if ($type === 'received') {
                $requests = $friendRequest->getReceivedRequests($userId);
            } elseif ($type === 'sent') {
                $requests = $friendRequest->getSentRequests($userId);
            } else {
                $response['message'] = 'Type invalide (received ou sent)';
                break;
            }

            $response['success'] = true;
            $response['data'] = $requests;
            $response['count'] = count($requests);
            break;

        case 'countPendingRequests':
            // Compter les demandes en attente
            $friendRequest = new FriendRequest($db);
            $count = $friendRequest->countPendingRequests($userId);

            $response['success'] = true;
            $response['count'] = $count;
            break;

        case 'respondToRequest':
            // Accepter ou rejeter une demande
            $requestId = $data['request_id'] ?? 0;
            $action_type = $data['action_type'] ?? ''; // 'accept' ou 'reject'

            if (!$requestId || !in_array($action_type, ['accept', 'reject'])) {
                $response['message'] = 'ID de demande et action requis (accept/reject)';
                break;
            }

            $friendRequest = new FriendRequest($db);

            if ($action_type === 'accept') {
                if ($friendRequest->acceptRequest($requestId, $userId)) {
                    // Créer la relation d'amitié
                    $request = $friendRequest->getRequestById($requestId);
                    if ($request) {
                        $friend = new Friend($db);
                        if ($friend->createFriendship($request['sender_id'], $request['receiver_id'])) {
                            $response['success'] = true;
                            $response['message'] = 'Demande acceptée, vous êtes maintenant amis';
                        } else {
                            $response['message'] = 'Demande acceptée mais erreur lors de la création de l\'amitié';
                        }
                    }
                } else {
                    $response['message'] = 'Impossible d\'accepter la demande';
                }
            } else {
                // reject
                if ($friendRequest->rejectRequest($requestId, $userId)) {
                    $response['success'] = true;
                    $response['message'] = 'Demande rejetée';
                } else {
                    $response['message'] = 'Impossible de rejeter la demande';
                }
            }
            break;

        case 'cancelFriendRequest':
            // Annuler une demande envoyée
            $requestId = $data['request_id'] ?? 0;

            if (!$requestId) {
                $response['message'] = 'ID de demande requis';
                break;
            }

            $friendRequest = new FriendRequest($db);
            if ($friendRequest->cancelRequest($requestId, $userId)) {
                $response['success'] = true;
                $response['message'] = 'Demande annulée';
            } else {
                $response['message'] = 'Impossible d\'annuler la demande';
            }
            break;

        case 'getFriendsList':
            // Obtenir la liste des amis
            $limit = $data['limit'] ?? $_GET['limit'] ?? 20;
            $offset = $data['offset'] ?? $_GET['offset'] ?? 0;

            $friend = new Friend($db);
            $friends = $friend->getFriendsList($userId, $limit, $offset);
            $totalCount = $friend->countFriends($userId);

            $response['success'] = true;
            $response['data'] = $friends;
            $response['count'] = count($friends);
            $response['total'] = $totalCount;
            $response['hasMore'] = ($offset + $limit) < $totalCount;
            break;

        case 'removeFriend':
            // Supprimer un ami
            $friendId = $data['friend_id'] ?? 0;

            if (!$friendId) {
                $response['message'] = 'ID de l\'ami requis';
                break;
            }

            $friend = new Friend($db);
            if ($friend->removeFriendship($userId, $friendId)) {
                $response['success'] = true;
                $response['message'] = 'Ami retiré';
            } else {
                $response['message'] = 'Impossible de retirer cet ami';
            }
            break;

        case 'getFriendProfile':
            // Voir le profil d'un ami
            $friendId = $data['friend_id'] ?? $_GET['friend_id'] ?? 0;

            if (!$friendId) {
                $response['message'] = 'ID de l\'ami requis';
                break;
            }

            // Vérifier qu'ils sont amis ou que le profil est public
            $friend = new Friend($db);
            $areFriends = $friend->areFriends($userId, $friendId);

            $stmt = $db->prepare("SELECT profile_visibility FROM users WHERE id = ?");
            $stmt->execute([$friendId]);
            $privacySettings = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$privacySettings) {
                $response['message'] = 'Utilisateur non trouvé';
                break;
            }

            $canView = false;
            if ($privacySettings['profile_visibility'] === 'public' || $areFriends) {
                $canView = true;
            }

            if (!$canView) {
                $response['message'] = 'Vous n\'avez pas la permission de voir ce profil';
                break;
            }

            // Récupérer le profil
            $stmt = $db->prepare("
                SELECT
                    id, username, guide_name, avatar_url, level, xp,
                    current_stage, created_at, last_login
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$friendId]);
            $profileData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profileData) {
                // Récupérer les statistiques
                $stmt = $db->prepare("
                    SELECT
                        COUNT(DISTINCT stage_id) as stages_completed,
                        SUM(score) as total_score,
                        AVG(score) as average_score
                    FROM stage_progress
                    WHERE user_id = ? AND completed = 1
                ");
                $stmt->execute([$friendId]);
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);

                // Amis mutuels
                $mutualFriends = $friend->getMutualFriends($userId, $friendId);

                $response['success'] = true;
                $response['data'] = array_merge($profileData, [
                    'stages_completed' => $stats['stages_completed'] ?? 0,
                    'total_score' => $stats['total_score'] ?? 0,
                    'average_score' => round($stats['average_score'] ?? 0, 1),
                    'mutual_friends' => $mutualFriends,
                    'mutual_friends_count' => count($mutualFriends)
                ]);
            } else {
                $response['message'] = 'Profil non trouvé';
            }
            break;

        case 'compareFriendScores':
            // Comparer les scores avec un ami
            $friendId = $data['friend_id'] ?? $_GET['friend_id'] ?? 0;
            $stageId = $data['stage_id'] ?? $_GET['stage_id'] ?? null;

            if (!$friendId) {
                $response['message'] = 'ID de l\'ami requis';
                break;
            }

            // Vérifier qu'ils sont amis
            $friend = new Friend($db);
            if (!$friend->areFriends($userId, $friendId)) {
                $response['message'] = 'Vous devez être amis pour comparer les scores';
                break;
            }

            if ($stageId) {
                // Comparaison pour un stage spécifique
                $stmt = $db->prepare("
                    SELECT
                        user_id,
                        MAX(score) as best_score,
                        MIN(time_spent) as best_time,
                        AVG(score) as average_score,
                        COUNT(*) as attempts
                    FROM stage_progress
                    WHERE user_id IN (?, ?) AND stage_id = ?
                    GROUP BY user_id
                ");
                $stmt->execute([$userId, $friendId, $stageId]);
            } else {
                // Comparaison globale
                $stmt = $db->prepare("
                    SELECT
                        user_id,
                        SUM(score) as total_score,
                        COUNT(DISTINCT stage_id) as stages_completed,
                        AVG(score) as average_score
                    FROM stage_progress
                    WHERE user_id IN (?, ?) AND completed = 1
                    GROUP BY user_id
                ");
                $stmt->execute([$userId, $friendId]);
            }

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Organiser les résultats
            $comparison = [
                'user' => null,
                'friend' => null
            ];

            foreach ($results as $result) {
                if ($result['user_id'] == $userId) {
                    $comparison['user'] = $result;
                } else {
                    $comparison['friend'] = $result;
                }
            }

            // Ajouter les informations de base des utilisateurs
            $stmt = $db->prepare("SELECT id, username, guide_name, avatar_url, level FROM users WHERE id IN (?, ?)");
            $stmt->execute([$userId, $friendId]);
            $usersInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usersInfo as $userInfo) {
                if ($userInfo['id'] == $userId) {
                    $comparison['user'] = array_merge($comparison['user'] ?? [], $userInfo);
                } else {
                    $comparison['friend'] = array_merge($comparison['friend'] ?? [], $userInfo);
                }
            }

            $response['success'] = true;
            $response['data'] = $comparison;
            $response['stage_id'] = $stageId;
            break;

        case 'getMutualFriends':
            // Obtenir les amis mutuels
            $friendId = $data['friend_id'] ?? $_GET['friend_id'] ?? 0;

            if (!$friendId) {
                $response['message'] = 'ID de l\'ami requis';
                break;
            }

            $friend = new Friend($db);
            $mutualFriends = $friend->getMutualFriends($userId, $friendId);

            $response['success'] = true;
            $response['data'] = $mutualFriends;
            $response['count'] = count($mutualFriends);
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
