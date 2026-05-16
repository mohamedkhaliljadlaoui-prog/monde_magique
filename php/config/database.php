<?php
// database.php - Configuration de la base de données

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            // Charger la configuration depuis .env ou config
            $config = $this->loadConfig();
            
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_PERSISTENT => true // Connexion persistante
            ];
            
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
            
            // Définir le fuseau horaire
            $this->connection->exec("SET time_zone = '+01:00'");
            
        } catch (PDOException $e) {
            error_log("Erreur connexion DB: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    private function loadConfig() {
        // Essayer de charger depuis .env
        if (file_exists(__DIR__ . '/../../.env')) {
            $env = parse_ini_file(__DIR__ . '/../../.env');
            return [
                'host' => $env['DB_HOST'] ?? 'localhost',
                'database' => $env['DB_NAME'] ?? 'monde_magique',
                'username' => $env['DB_USER'] ?? 'root',
                'password' => $env['DB_PASS'] ?? '',
                'port' => $env['DB_PORT'] ?? '3306'
            ];
        }
        
        // Configuration par défaut pour développement
        return [
            'host' => 'localhost',
            'database' => 'monde_magique',
            'username' => 'root',
            'password' => '',
            'port' => '3306'
        ];
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Méthodes utilitaires
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollBack() {
        return $this->connection->rollBack();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    // Exécuter une requête avec paramètres
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur SQL: $sql - " . $e->getMessage());
            throw $e;
        }
    }
    
    // Récupérer une seule ligne
    public function fetchOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }
    
    // Récupérer plusieurs lignes
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Insérer des données
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->execute($sql, $data);
        
        return $this->lastInsertId();
    }
    
    // Mettre à jour des données
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = :$key";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }
    
    // Supprimer des données
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }
    
    // Vérifier si une table existe
    public function tableExists($tableName) {
        $sql = "SHOW TABLES LIKE ?";
        $result = $this->fetchOne($sql, [$tableName]);
        return !empty($result);
    }
    
    // Backup de la base de données
    public function backup($backupPath) {
        $config = $this->loadConfig();
        $backupFile = $backupPath . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        $command = "mysqldump --user={$config['username']} --password={$config['password']} " .
                   "--host={$config['host']} {$config['database']} > $backupFile";
        
        system($command, $output);
        
        if ($output === 0 && file_exists($backupFile)) {
            return $backupFile;
        }
        
        return false;
    }
    
    // Optimiser les tables
    public function optimizeTables() {
        $tables = $this->fetchAll("SHOW TABLES");
        
        foreach ($tables as $table) {
            $tableName = current($table);
            $this->execute("OPTIMIZE TABLE $tableName");
        }
        
        return count($tables);
    }
}

// Fonction helper pour obtenir la connexion
function getDB() {
    return Database::getInstance()->getConnection();
}