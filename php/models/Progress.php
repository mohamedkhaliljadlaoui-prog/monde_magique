<?php
// ===================================
// PROGRESS.PHP - Progress Model
// ===================================

class Progress {
    private $conn;
    private $table = 'user_progress';

    public $id;
    public $user_id;
    public $stage_id;
    public $station_id;
    public $completed;
    public $score;
    public $time_spent;
    public $attempts;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Save progress for a station
     */
    public function saveProgress() {
        // Check if progress exists
        $existing = $this->getProgress();

        if ($existing) {
            return $this->updateProgress();
        } else {
            return $this->createProgress();
        }
    }

    /**
     * Create new progress entry
     */
    private function createProgress() {
        $query = "INSERT INTO " . $this->table . "
                  SET user_id=:user_id,
                      stage_id=:stage_id,
                      station_id=:station_id,
                      completed=:completed,
                      score=:score,
                      time_spent=:time_spent,
                      attempts=1,
                      completed_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->bindParam(':station_id', $this->station_id);
        $stmt->bindParam(':completed', $this->completed);
        $stmt->bindParam(':score', $this->score);
        $stmt->bindParam(':time_spent', $this->time_spent);

        return $stmt->execute();
    }

    /**
     * Update existing progress
     */
    private function updateProgress() {
        $query = "UPDATE " . $this->table . "
                  SET completed=:completed,
                      score=:score,
                      time_spent=:time_spent,
                      attempts=attempts+1,
                      completed_at=NOW()
                  WHERE user_id=:user_id 
                  AND stage_id=:stage_id 
                  AND station_id=:station_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->bindParam(':station_id', $this->station_id);
        $stmt->bindParam(':completed', $this->completed);
        $stmt->bindParam(':score', $this->score);
        $stmt->bindParam(':time_spent', $this->time_spent);

        return $stmt->execute();
    }

    /**
     * Get progress for specific station
     */
    public function getProgress() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id=:user_id 
                  AND stage_id=:stage_id 
                  AND station_id=:station_id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->bindParam(':station_id', $this->station_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all progress for a stage
     */
    public function getStageProgress() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id=:user_id AND stage_id=:stage_id
                  ORDER BY station_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all user progress
     */
    public function getAllProgress() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id=:user_id
                  ORDER BY stage_id, station_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if stage is completed
     */
    public function isStageCompleted() {
        $query = "SELECT COUNT(*) as total,
                         SUM(CASE WHEN completed=1 THEN 1 ELSE 0 END) as completed
                  FROM " . $this->table . "
                  WHERE user_id=:user_id AND stage_id=:stage_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Stage has 7 stations
        return $result['completed'] >= 7;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage() {
        $query = "SELECT COUNT(*) as total,
                         SUM(CASE WHEN completed=1 THEN 1 ELSE 0 END) as completed
                  FROM " . $this->table . "
                  WHERE user_id=:user_id AND stage_id=:stage_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] == 0) return 0;
        
        return ($result['completed'] / 7) * 100;
    }
}
?>
