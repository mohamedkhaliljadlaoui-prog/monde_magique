<?php
// ===================================
// USER.PHP - User Model
// ===================================

class User {
    private $conn;
    private $table = 'users';

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $guide_name;
    public $guide_gender;
    public $age;
    public $level;
    public $xp;
    public $coins;
    public $diamonds;
    public $current_stage;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new user
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET username=:username, 
                      email=:email, 
                      password=:password,
                      guide_name=:guide_name,
                      guide_gender=:guide_gender,
                      age=:age,
                      level=1,
                      xp=0,
                      coins=0,
                      diamonds=0,
                      current_stage=1";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->guide_name = htmlspecialchars(strip_tags($this->guide_name));
        $this->guide_gender = htmlspecialchars(strip_tags($this->guide_gender));

        // Bind values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':guide_name', $this->guide_name);
        $stmt->bindParam(':guide_gender', $this->guide_gender);
        $stmt->bindParam(':age', $this->age);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Read user by username
     */
    public function readByUsername() {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read user by ID
     */
    public function readById() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->guide_name = $row['guide_name'];
            $this->guide_gender = $row['guide_gender'];
            $this->age = $row['age'];
            $this->level = $row['level'];
            $this->xp = $row['xp'];
            $this->coins = $row['coins'];
            $this->diamonds = $row['diamonds'];
            $this->current_stage = $row['current_stage'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    /**
     * Update user
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET level=:level,
                      xp=:xp,
                      coins=:coins,
                      diamonds=:diamonds,
                      current_stage=:current_stage
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':xp', $this->xp);
        $stmt->bindParam(':coins', $this->coins);
        $stmt->bindParam(':diamonds', $this->diamonds);
        $stmt->bindParam(':current_stage', $this->current_stage);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Add XP and check for level up
     */
    public function addXP($amount) {
        $this->xp += $amount;
        $xpNeeded = $this->level * 100;

        if ($this->xp >= $xpNeeded) {
            $this->level++;
            return ['levelUp' => true, 'newLevel' => $this->level];
        }

        return ['levelUp' => false];
    }

    /**
     * Add coins
     */
    public function addCoins($amount) {
        $this->coins += $amount;
        return $this->update();
    }

    /**
     * Remove coins
     */
    public function removeCoins($amount) {
        if ($this->coins >= $amount) {
            $this->coins -= $amount;
            return $this->update();
        }
        return false;
    }

    /**
     * Add diamonds
     */
    public function addDiamonds($amount) {
        $this->diamonds += $amount;
        return $this->update();
    }

    /**
     * Remove diamonds
     */
    public function removeDiamonds($amount) {
        if ($this->diamonds >= $amount) {
            $this->diamonds -= $amount;
            return $this->update();
        }
        return false;
    }

    /**
     * Check if username exists
     */
    public function usernameExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>
