<?php
// ===================================
// QUIZ.PHP - Quiz Model  
// ===================================

class Quiz {
    private $conn;
    private $table = 'quiz_questions';

    public $id;
    public $stage_id;
    public $station_id;
    public $question;
    public $question_ar;
    public $type; // 'text', 'image', 'multiple'
    public $options;
    public $correct_answer;
    public $points;
    public $difficulty;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get questions for stage/station
     */
    public function getQuestions() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE stage_id = :stage_id";

        if ($this->station_id) {
            $query .= " AND station_id = :station_id";
        }

        $query .= " ORDER BY RAND()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stage_id', $this->stage_id);
        
        if ($this->station_id) {
            $stmt->bindParam(':station_id', $this->station_id);
        }

        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse options JSON
        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
        }

        return $questions;
    }

    /**
     * Get random questions
     */
    public function getRandomQuestions($limit = 20) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE stage_id = :stage_id
                  ORDER BY RAND()
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
        }

        return $questions;
    }

    /**
     * Validate answer
     */
    public function validateAnswer($questionId, $userAnswer) {
        $query = "SELECT correct_answer, points FROM " . $this->table . "
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $questionId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $isCorrect = ($result['correct_answer'] == $userAnswer);
            return [
                'correct' => $isCorrect,
                'points' => $isCorrect ? $result['points'] : 0,
                'correct_answer' => $result['correct_answer']
            ];
        }

        return ['correct' => false, 'points' => 0];
    }

    /**
     * Save quiz result
     */
    public function saveResult($userId, $score, $totalQuestions, $timeSpent) {
        $query = "INSERT INTO quiz_results
                  SET user_id = :user_id,
                      stage_id = :stage_id,
                      station_id = :station_id,
                      score = :score,
                      total_questions = :total_questions,
                      time_spent = :time_spent,
                      completed_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->bindParam(':station_id', $this->station_id);
        $stmt->bindParam(':score', $score);
        $stmt->bindParam(':total_questions', $totalQuestions);
        $stmt->bindParam(':time_spent', $timeSpent);

        return $stmt->execute();
    }

    /**
     * Get user quiz history
     */
    public function getUserHistory($userId) {
        $query = "SELECT * FROM quiz_results
                  WHERE user_id = :user_id
                  ORDER BY completed_at DESC
                  LIMIT 50";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get best score for stage
     */
    public function getBestScore($userId) {
        $query = "SELECT MAX(score) as best_score FROM quiz_results
                  WHERE user_id = :user_id AND stage_id = :stage_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':stage_id', $this->stage_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['best_score'] ?? 0;
    }
}
?>
