<?php
// Script pour corriger/mettre à jour la base de données

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Correction de la base de données</h2>";

try {
    require_once 'config/database.php';
    $db = Database::getInstance()->getConnection();
    
    echo "<p style='color: green;'>✅ Connexion réussie</p>";
    
    // Vérifier les colonnes actuelles
    $stmt = $db->query("SHOW COLUMNS FROM users");
    $existingColumns = [];
    while ($row = $stmt->fetch()) {
        $existingColumns[$row['Field']] = $row['Type'];
    }
    
    echo "<h3>📋 Colonnes actuelles:</h3>";
    echo "<ul>";
    foreach ($existingColumns as $col => $type) {
        echo "<li><strong>$col</strong>: $type</li>";
    }
    echo "</ul>";
    
    // Colonnes nécessaires pour le système
    $neededColumns = [
        'id' => true,
        'username' => true,
        'email' => true,
        'password' => 'password ou password_hash',
        'gender' => 'gender ou guide_gender',
        'level' => true,
        'xp' => true,
        'coins' => true,
        'diamonds' => true,
        'current_stage' => true,
        'created_at' => true
    ];
    
    echo "<h3>✅ Validation des colonnes essentielles:</h3>";
    echo "<ul>";
    
    $allGood = true;
    
    // Vérifier password
    if (isset($existingColumns['password']) || isset($existingColumns['password_hash'])) {
        $passCol = isset($existingColumns['password_hash']) ? 'password_hash' : 'password';
        echo "<li>✅ Mot de passe: <strong>$passCol</strong></li>";
    } else {
        echo "<li>❌ Aucune colonne de mot de passe trouvée!</li>";
        $allGood = false;
    }
    
    // Vérifier gender
    if (isset($existingColumns['gender']) || isset($existingColumns['guide_gender'])) {
        $genderCol = isset($existingColumns['guide_gender']) ? 'guide_gender' : 'gender';
        echo "<li>✅ Genre: <strong>$genderCol</strong></li>";
    } else {
        echo "<li>❌ Aucune colonne de genre trouvée!</li>";
        $allGood = false;
    }
    
    // Vérifier les autres colonnes
    foreach (['id', 'username', 'email', 'level', 'xp', 'coins', 'diamonds', 'current_stage', 'created_at'] as $col) {
        if (isset($existingColumns[$col])) {
            echo "<li>✅ $col</li>";
        } else {
            echo "<li>❌ $col manquante!</li>";
            $allGood = false;
        }
    }
    
    echo "</ul>";
    
    // Colonnes optionnelles
    echo "<h3>📌 Colonnes optionnelles:</h3>";
    echo "<ul>";
    $optionalColumns = ['guide_name', 'age', 'language', 'birth_date', 'last_login'];
    foreach ($optionalColumns as $col) {
        if (isset($existingColumns[$col])) {
            echo "<li>✅ $col présente</li>";
        } else {
            echo "<li>⚠️ $col absente (optionnelle)</li>";
        }
    }
    echo "</ul>";
    
    if ($allGood) {
        echo "<h3 style='color: green;'>✅ La base de données est correctement configurée!</h3>";
        echo "<p>Vous pouvez maintenant tester l'inscription.</p>";
    } else {
        echo "<h3 style='color: red;'>❌ Des colonnes essentielles sont manquantes</h3>";
        echo "<p><strong>Solution:</strong> Exécutez le script SQL approprié:</p>";
        echo "<ul>";
        echo "<li>Pour un schéma simple: <code>database/schema-complete.sql</code></li>";
        echo "<li>Pour un schéma complet: <code>database/schema.sql</code></li>";
        echo "</ul>";
    }
    
    // Afficher le nombre d'utilisateurs
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    echo "<p>📊 Nombre d'utilisateurs enregistrés: <strong>$count</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

<hr>
<h3>🚀 Prochaines étapes:</h3>
<ol>
    <li><a href="test-db.php">Tester la connexion</a></li>
    <li><a href="api/auth/test-register-direct.php">Test d'inscription direct</a></li>
    <li><a href="../inscription.html">Formulaire d'inscription</a></li>
</ol>
