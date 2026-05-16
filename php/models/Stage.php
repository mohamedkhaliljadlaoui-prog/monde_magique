<?php
// ===================================
// STAGE.PHP - Stage Model
// ===================================

class Stage {
    private $conn;
    private $table = 'stages';

    public $id;
    public $name;
    public $name_ar;
    public $region;
    public $icon;
    public $latitude;
    public $longitude;
    public $order_num;
    public $required_level;
    public $total_stations;
    public $theme_color;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all stages
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY order_num";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get stage by ID
     */
    public function getById() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->name_ar = $row['name_ar'];
            $this->region = $row['region'];
            $this->icon = $row['icon'];
            $this->latitude = $row['latitude'];
            $this->longitude = $row['longitude'];
            $this->order_num = $row['order_num'];
            $this->required_level = $row['required_level'];
            $this->total_stations = $row['total_stations'];
            $this->theme_color = $row['theme_color'];
            return true;
        }

        return false;
    }

    /**
     * Get stages available for user
     */
    public function getAvailableForUser($userId, $userLevel) {
        $query = "SELECT s.*, 
                         (SELECT COUNT(*) FROM user_progress up 
                          WHERE up.user_id = :user_id 
                          AND up.stage_id = s.id 
                          AND up.completed = 1) as completed_stations,
                         CASE 
                            WHEN s.required_level <= :user_level THEN 1
                            ELSE 0
                         END as unlocked
                  FROM " . $this->table . " s
                  ORDER BY s.order_num";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':user_level', $userLevel);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get stage stations
     */
    public function getStations() {
        $query = "SELECT * FROM stations WHERE stage_id = :stage_id ORDER BY order_num";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stage_id', $this->id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get stage content
     */
    public function getContent() {
        $query = "SELECT * FROM stage_content WHERE stage_id = :stage_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stage_id', $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
