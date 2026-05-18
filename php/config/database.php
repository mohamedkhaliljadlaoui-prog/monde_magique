<?php
// database.php - Configuration de la base de données

class Database {
    private static $instance = null;
    private $connection;
    
    public function __construct() {
        // Charger la configuration depuis .env ou config
        $config = $this->loadConfig();

        $host = $config['host'] ?? 'localhost';
        $database = $config['database'] ?? 'monde_magique';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $port = $config['port'] ?? '3306';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_PERSISTENT => true
        ];

        $hostsToTry = [$host];
        if ($host === 'localhost') {
            $hostsToTry[] = '127.0.0.1';
        } elseif ($host === '127.0.0.1') {
            $hostsToTry[] = 'localhost';
        }

        $portsToTry = [$port, '3306', '3307', '3308'];
        $portsToTry = array_values(array_unique(array_filter($portsToTry, fn($p) => $p !== null && $p !== '')));

        $lastException = null;
        $connectedHost = null;
        $connectedPort = null;

        foreach ($hostsToTry as $h) {
            foreach ($portsToTry as $p) {
                try {
                    $dsn = "mysql:host={$h};port={$p};dbname={$database};charset=utf8mb4";
                    $this->connection = new PDO($dsn, $username, $password, $options);
                    $connectedHost = $h;
                    $connectedPort = $p;
                    break 2;
                } catch (PDOException $e) {
                    $lastException = $e;
                    $errorCode = (int)($e->errorInfo[1] ?? 0);

                    // 1049 = Unknown database -> tenter de la créer, puis reconnecter
                    if ($errorCode === 1049) {
                        try {
                            $dsnNoDb = "mysql:host={$h};port={$p};charset=utf8mb4";
                            $tmp = new PDO($dsnNoDb, $username, $password, $options);

                            $safeDb = preg_replace('/[^a-zA-Z0-9_]/', '', $database);
                            if ($safeDb === '') {
                                throw new Exception('Nom de base de données invalide');
                            }
                            $tmp->exec("CREATE DATABASE IF NOT EXISTS `{$safeDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                            $this->connection = new PDO("mysql:host={$h};port={$p};dbname={$safeDb};charset=utf8mb4", $username, $password, $options);
                            $connectedHost = $h;
                            $connectedPort = $p;
                            break 2;
                        } catch (Throwable $inner) {
                            $lastException = $inner;
                            continue;
                        }
                    }

                    // 2002/2006/1045 etc. -> continuer les essais
                    continue;
                }
            }
        }

        if (!$this->connection) {
            if ($lastException instanceof PDOException) {
                error_log("Erreur connexion DB (PDO): " . $lastException->getMessage());
            } elseif ($lastException) {
                error_log("Erreur connexion DB: " . $lastException->getMessage());
            }
            $msg = "Impossible de se connecter à la base de données";
            $msg .= " (vérifiez MySQL dans XAMPP, host={$host}, port={$port}, db={$database})";
            throw new Exception($msg);
        }

        // Définir le fuseau horaire (ne pas bloquer si non autorisé)
        try {
            $this->connection->exec("SET time_zone = '+01:00'");
        } catch (Throwable $e) {
        }

        // Assurer un schéma minimal (users) pour l'auth
        $this->ensureSchema();
    }

    private function ensureSchema() {
        // Table users requise pour register/login
        try {
            $stmt = $this->connection->prepare("SHOW TABLES LIKE 'users'");
            $stmt->execute();
            $hasUsers = $stmt->rowCount() > 0;

            if (!$hasUsers) {
                $this->connection->exec(
                    "CREATE TABLE IF NOT EXISTS users (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        username VARCHAR(50) UNIQUE NOT NULL,
                        email VARCHAR(100) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        gender VARCHAR(20) NULL,
                        birth_date DATE NULL,
                        age INT NULL,
                        guide_name VARCHAR(50) NULL,
                        language VARCHAR(10) NULL,
                        parent_email VARCHAR(100) NULL,
                        level INT NOT NULL DEFAULT 1,
                        xp INT NOT NULL DEFAULT 0,
                        coins INT NOT NULL DEFAULT 0,
                        diamonds INT NOT NULL DEFAULT 0,
                        current_stage INT NOT NULL DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        last_login TIMESTAMP NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
                );
                return;
            }

            $existing = [];
            $cols = $this->connection->query("SHOW COLUMNS FROM users");
            while ($row = $cols->fetch()) {
                $existing[$row['Field']] = true;
            }

            $migrations = [
                'password' => "ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL",
                'gender' => "ALTER TABLE users ADD COLUMN gender VARCHAR(20) NULL",
                'birth_date' => "ALTER TABLE users ADD COLUMN birth_date DATE NULL",
                'age' => "ALTER TABLE users ADD COLUMN age INT NULL",
                'guide_name' => "ALTER TABLE users ADD COLUMN guide_name VARCHAR(50) NULL",
                'language' => "ALTER TABLE users ADD COLUMN language VARCHAR(10) NULL",
                'parent_email' => "ALTER TABLE users ADD COLUMN parent_email VARCHAR(100) NULL",
                'level' => "ALTER TABLE users ADD COLUMN level INT NOT NULL DEFAULT 1",
                'xp' => "ALTER TABLE users ADD COLUMN xp INT NOT NULL DEFAULT 0",
                'coins' => "ALTER TABLE users ADD COLUMN coins INT NOT NULL DEFAULT 0",
                'diamonds' => "ALTER TABLE users ADD COLUMN diamonds INT NOT NULL DEFAULT 0",
                'current_stage' => "ALTER TABLE users ADD COLUMN current_stage INT NOT NULL DEFAULT 1",
                'created_at' => "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                'last_login' => "ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL"
            ];

            foreach ($migrations as $col => $sql) {
                if (!isset($existing[$col])) {
                    try {
                        $this->connection->exec($sql);
                    } catch (Throwable $e) {
                        // ignorer
                    }
                }
            }
        } catch (Throwable $e) {
            // ne pas bloquer l'app pour un souci de migration
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
                   "--host={$config['host']} --port={$config['port']} {$config['database']} > $backupFile";
        
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