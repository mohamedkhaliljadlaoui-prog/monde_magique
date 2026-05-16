<?php
// test-db.php - Test de connexion à la base de données

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de connexion à la base de données</h2>";

try {
    require_once 'config/database.php';
    
    $db = Database::getInstance()->getConnection();
    
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie!</p>";
    
    // Vérifier si la table users existe
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table 'users' trouvée</p>";
        
        // Afficher la structure de la table
        $stmt = $db->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Structure de la table 'users':</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
        
        $hasPasswordHash = false;
        $hasPassword = false;
        
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong></td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
            
            if ($col['Field'] === 'password_hash') $hasPasswordHash = true;
            if ($col['Field'] === 'password') $hasPassword = true;
        }
        echo "</table>";
        
        echo "<h3>🔍 Diagnostic du mot de passe:</h3>";
        if ($hasPasswordHash) {
            echo "<p style='color: blue;'>➡️ Colonne trouvée: <strong>password_hash</strong></p>";
        }
        if ($hasPassword) {
            echo "<p style='color: blue;'>➡️ Colonne trouvée: <strong>password</strong></p>";
        }
        if (!$hasPasswordHash && !$hasPassword) {
            echo "<p style='color: red;'>❌ Aucune colonne de mot de passe trouvée!</p>";
        }
        
        // Compter les utilisateurs
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Nombre d'utilisateurs enregistrés: <strong>{$count}</strong></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Table 'users' non trouvée</p>";
        echo "<p>Veuillez exécuter le script SQL: database/schema-complete.sql</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
