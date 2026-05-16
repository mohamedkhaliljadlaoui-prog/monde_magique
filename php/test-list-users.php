<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    // Détecter les colonnes
    $stmt = $db->query("SHOW COLUMNS FROM users");
    $columns = [];
    while ($row = $stmt->fetch()) {
        $columns[] = $row['Field'];
    }
    
    $passwordCol = in_array('password_hash', $columns) ? 'password_hash' : 'password';
    $genderCol = in_array('guide_gender', $columns) ? 'guide_gender' : 'gender';
    
    // Récupérer tous les utilisateurs
    $sql = "SELECT id, username, email, {$genderCol} as gender, level, coins, diamonds, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT 10";
    
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Utilisateurs dans la base de données:</h3>";
    
    if (empty($users)) {
        echo "<p style='color: orange;'>⚠️ Aucun utilisateur trouvé</p>";
        echo "<p>Utilisez le bouton 'Créer Test User' pour créer un utilisateur de test</p>";
    } else {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #667eea; color: white;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>Genre</th><th>Level</th><th>Coins</th><th>Créé le</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td><strong>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['gender']}</td>";
            echo "<td>{$user['level']}</td>";
            echo "<td>{$user['coins']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<p style='margin-top: 15px;'>Total: <strong>" . count($users) . "</strong> utilisateur(s)</p>";
    }
    
    // Compter le total
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $total = $stmt->fetch()['total'];
    echo "<p>Total dans la base: <strong>$total</strong> utilisateur(s)</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>
