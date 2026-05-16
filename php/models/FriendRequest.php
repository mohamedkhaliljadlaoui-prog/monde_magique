<?php
// ===================================
// FRIENDREQUEST.PHP - Friend Request Model
// Gestion des demandes d'amis
// ===================================

class FriendRequest {
    private $conn;
    private $table = 'friend_requests';

    // Friend request properties
    public $id;
    public $sender_id;
    public $receiver_id;
    public $status;
    public $message;
    public $created_at;
    public $responded_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Envoyer une demande d'ami
     * @param int $sender_id ID de l'expéditeur
     * @param int $receiver_id ID du destinataire
     * @param string $message Message optionnel
     * @return mixed ID de la demande créée ou false
     */
    public function sendRequest($sender_id, $receiver_id, $message = '') {
        // Vérifier que l'utilisateur ne s'envoie pas une demande à lui-même
        if ($sender_id == $receiver_id) {
            return false;
        }

        // Vérifier qu'il n'existe pas déjà une demande en attente
        if ($this->hasPendingRequest($sender_id, $receiver_id)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . "
                  (sender_id, receiver_id, message, status)
                  VALUES (:sender_id, :receiver_id, :message, 'pending')";

        $stmt = $this->conn->prepare($query);

        $message = htmlspecialchars(strip_tags($message));

        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Vérifier s'il existe une demande en attente entre deux utilisateurs
     * @param int $sender_id ID de l'expéditeur
     * @param int $receiver_id ID du destinataire
     * @return bool True si demande en attente existe
     */
    public function hasPendingRequest($sender_id, $receiver_id) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . "
                  WHERE ((sender_id = :sender_id AND receiver_id = :receiver_id)
                     OR (sender_id = :receiver_id AND receiver_id = :sender_id))
                    AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'] > 0;
    }

    /**
     * Obtenir les demandes reçues par un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @param string $status Statut des demandes (default: 'pending')
     * @return array Liste des demandes avec infos de l'expéditeur
     */
    public function getReceivedRequests($user_id, $status = 'pending') {
        $query = "SELECT
                    fr.id as request_id,
                    fr.sender_id,
                    fr.message,
                    fr.created_at,
                    u.username,
                    u.guide_name,
                    u.avatar_url,
                    u.level,
                    u.xp
                  FROM " . $this->table . " fr
                  INNER JOIN users u ON fr.sender_id = u.id
                  WHERE fr.receiver_id = :user_id
                    AND fr.status = :status
                  ORDER BY fr.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les demandes envoyées par un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @param string $status Statut des demandes (default: 'pending')
     * @return array Liste des demandes avec infos du destinataire
     */
    public function getSentRequests($user_id, $status = 'pending') {
        $query = "SELECT
                    fr.id as request_id,
                    fr.receiver_id,
                    fr.message,
                    fr.created_at,
                    u.username,
                    u.guide_name,
                    u.avatar_url,
                    u.level,
                    u.xp
                  FROM " . $this->table . " fr
                  INNER JOIN users u ON fr.receiver_id = u.id
                  WHERE fr.sender_id = :user_id
                    AND fr.status = :status
                  ORDER BY fr.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les demandes en attente pour un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @return int Nombre de demandes en attente
     */
    public function countPendingRequests($user_id) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . "
                  WHERE receiver_id = :user_id
                    AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    /**
     * Accepter une demande d'ami
     * @param int $request_id ID de la demande
     * @param int $user_id ID de l'utilisateur qui accepte (pour vérification)
     * @return bool Success status
     */
    public function acceptRequest($request_id, $user_id) {
        // Vérifier que l'utilisateur est bien le destinataire
        $request = $this->getRequestById($request_id);
        if (!$request || $request['receiver_id'] != $user_id) {
            return false;
        }

        $query = "UPDATE " . $this->table . "
                  SET status = 'accepted',
                      responded_at = NOW()
                  WHERE id = :request_id
                    AND receiver_id = :user_id
                    AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
     * Rejeter une demande d'ami
     * @param int $request_id ID de la demande
     * @param int $user_id ID de l'utilisateur qui rejette (pour vérification)
     * @return bool Success status
     */
    public function rejectRequest($request_id, $user_id) {
        // Vérifier que l'utilisateur est bien le destinataire
        $request = $this->getRequestById($request_id);
        if (!$request || $request['receiver_id'] != $user_id) {
            return false;
        }

        $query = "UPDATE " . $this->table . "
                  SET status = 'rejected',
                      responded_at = NOW()
                  WHERE id = :request_id
                    AND receiver_id = :user_id
                    AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
     * Annuler une demande d'ami envoyée
     * @param int $request_id ID de la demande
     * @param int $user_id ID de l'utilisateur qui annule (pour vérification)
     * @return bool Success status
     */
    public function cancelRequest($request_id, $user_id) {
        // Vérifier que l'utilisateur est bien l'expéditeur
        $request = $this->getRequestById($request_id);
        if (!$request || $request['sender_id'] != $user_id) {
            return false;
        }

        $query = "UPDATE " . $this->table . "
                  SET status = 'cancelled',
                      responded_at = NOW()
                  WHERE id = :request_id
                    AND sender_id = :user_id
                    AND status = 'pending'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
     * Obtenir une demande par son ID
     * @param int $request_id ID de la demande
     * @return array|false Données de la demande ou false
     */
    public function getRequestById($request_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE id = :request_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les demandes envoyées aujourd'hui par un utilisateur (rate limiting)
     * @param int $user_id ID de l'utilisateur
     * @return int Nombre de demandes envoyées aujourd'hui
     */
    public function countRequestsToday($user_id) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . "
                  WHERE sender_id = :user_id
                    AND DATE(created_at) = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    /**
     * Nettoyer les anciennes demandes rejetées/annulées (maintenance)
     * @param int $days Nombre de jours à conserver (default: 30)
     * @return bool Success status
     */
    public function cleanOldRequests($days = 30) {
        $query = "DELETE FROM " . $this->table . "
                  WHERE status IN ('rejected', 'cancelled')
                    AND responded_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
