<?php
// ===================================
// TRANSACTION.PHP - Transaction Model
// ===================================

class Transaction {
    private $conn;
    private $table = 'transactions';

    public $id;
    public $user_id;
    public $type; // 'purchase', 'reward', 'penalty'
    public $category; // 'vehicle', 'clothes', 'accessory'
    public $item_id;
    public $amount;
    public $currency; // 'coins', 'diamonds'
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create transaction
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET user_id=:user_id,
                      type=:type,
                      category=:category,
                      item_id=:item_id,
                      amount=:amount,
                      currency=:currency,
                      description=:description,
                      created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':currency', $this->currency);
        $stmt->bindParam(':description', $this->description);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Get user transactions
     */
    public function getUserTransactions($limit = 50) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id=:user_id
                  ORDER BY created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get purchases
     */
    public function getPurchases() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id=:user_id AND type='purchase'
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get owned items
     */
    public function getOwnedItems() {
        $query = "SELECT DISTINCT item_id, category FROM " . $this->table . "
                  WHERE user_id=:user_id AND type='purchase'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $row['item_id'];
        }

        return $items;
    }

    /**
     * Check if item is owned
     */
    public function isItemOwned($itemId, $category) {
        $query = "SELECT id FROM " . $this->table . "
                  WHERE user_id=:user_id 
                  AND item_id=:item_id 
                  AND category=:category
                  AND type='purchase'
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':item_id', $itemId);
        $stmt->bindParam(':category', $category);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Get total spent
     */
    public function getTotalSpent($currency = 'coins') {
        $query = "SELECT SUM(amount) as total FROM " . $this->table . "
                  WHERE user_id=:user_id 
                  AND type='purchase' 
                  AND currency=:currency";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':currency', $currency);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Get total earned
     */
    public function getTotalEarned($currency = 'coins') {
        $query = "SELECT SUM(amount) as total FROM " . $this->table . "
                  WHERE user_id=:user_id 
                  AND type='reward' 
                  AND currency=:currency";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':currency', $currency);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>
