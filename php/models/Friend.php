<?php
// ===================================
// FRIEND.PHP - Friend Model
// Gestion des relations d'amitié
// ===================================

class Friend {
    private $conn;
    private $table = 'friends';

    // Friend properties
    public $id;
    public $user_id;
    public $friend_id;
    public $friendship_status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une nouvelle relation d'amitié (bidirectionnelle)
     * @param int $user_id ID de l'utilisateur
     * @param int $friend_id ID de l'ami
     * @return bool Success status
     */
    public function createFriendship($user_id, $friend_id) {
        try {
            $this->conn->beginTransaction();

            // Créer la relation dans les deux sens
            $query = "INSERT INTO " . $this->table . "
                      (user_id, friend_id, friendship_status)
                      VALUES (:user_id, :friend_id, 'active')";

            // Relation user -> friend
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':friend_id', $friend_id);
            $stmt->execute();

            // Relation friend -> user (bidirectionnelle)
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $friend_id);
            $stmt->bindParam(':friend_id', $user_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Obtenir la liste des amis d'un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @param int $limit Nombre maximum d'amis à retourner
     * @param int $offset Offset pour la pagination
     * @return array Liste des amis avec leurs informations
     */
    public function getFriendsList($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT
                    u.id,
                    u.username,
                    u.guide_name,
                    u.avatar_url,
                    u.level,
                    u.xp,
                    u.last_login,
                    f.created_at as friends_since
                  FROM " . $this->table . " f
                  INNER JOIN users u ON f.friend_id = u.id
                  WHERE f.user_id = :user_id
                    AND f.friendship_status = 'active'
                  ORDER BY f.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter le nombre d'amis d'un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @return int Nombre d'amis
     */
    public function countFriends($user_id) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . "
                  WHERE user_id = :user_id
                    AND friendship_status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    /**
     * Vérifier si deux utilisateurs sont amis
     * @param int $user_id ID de l'utilisateur
     * @param int $friend_id ID de l'ami potentiel
     * @return bool True si amis, False sinon
     */
    public function areFriends($user_id, $friend_id) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . "
                  WHERE user_id = :user_id
                    AND friend_id = :friend_id
                    AND friendship_status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'] > 0;
    }

    /**
     * Supprimer une relation d'amitié (bidirectionnelle)
     * @param int $user_id ID de l'utilisateur
     * @param int $friend_id ID de l'ami
     * @return bool Success status
     */
    public function removeFriendship($user_id, $friend_id) {
        try {
            $this->conn->beginTransaction();

            $query = "DELETE FROM " . $this->table . "
                      WHERE (user_id = :user_id AND friend_id = :friend_id)
                         OR (user_id = :friend_id AND friend_id = :user_id)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Bloquer un ami
     * @param int $user_id ID de l'utilisateur
     * @param int $friend_id ID de l'ami à bloquer
     * @return bool Success status
     */
    public function blockFriend($user_id, $friend_id) {
        $query = "UPDATE " . $this->table . "
                  SET friendship_status = 'blocked'
                  WHERE user_id = :user_id AND friend_id = :friend_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Obtenir les IDs de tous les amis d'un utilisateur (pour les requêtes rapides)
     * @param int $user_id ID de l'utilisateur
     * @return array Liste des IDs des amis
     */
    public function getFriendIds($user_id) {
        $query = "SELECT friend_id
                  FROM " . $this->table . "
                  WHERE user_id = :user_id
                    AND friendship_status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $ids = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ids[] = (int) $row['friend_id'];
        }

        return $ids;
    }

    /**
     * Obtenir les amis mutuels entre deux utilisateurs
     * @param int $user_id1 ID du premier utilisateur
     * @param int $user_id2 ID du second utilisateur
     * @return array Liste des amis mutuels
     */
    public function getMutualFriends($user_id1, $user_id2) {
        $query = "SELECT DISTINCT u.id, u.username, u.guide_name, u.avatar_url, u.level
                  FROM " . $this->table . " f1
                  INNER JOIN " . $this->table . " f2
                    ON f1.friend_id = f2.friend_id
                  INNER JOIN users u ON f1.friend_id = u.id
                  WHERE f1.user_id = :user_id1
                    AND f2.user_id = :user_id2
                    AND f1.friendship_status = 'active'
                    AND f2.friendship_status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id1', $user_id1, PDO::PARAM_INT);
        $stmt->bindParam(':user_id2', $user_id2, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
