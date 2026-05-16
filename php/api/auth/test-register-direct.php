<?php
// Test direct de l'API d'inscription

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Test direct de l'API d'inscription</h2>";

// Simuler une requête POST
$_SERVER['REQUEST_METHOD'] = 'POST';

$testData = [
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'password' => 'test123',
    'gender' => 'boy',
    'birth_date' => '2015-05-15',
    'guide_name' => 'تيو',
    'language' => 'ar'
];

echo "<h3>Données de test:</h3>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

// Simuler l'entrée JSON
$jsonInput = json_encode($testData);
echo "<h3>JSON envoyé:</h3>";
echo "<pre>$jsonInput</pre>";

// Créer un fichier temporaire avec ces données
file_put_contents('php://input', $jsonInput);

echo "<h3>Tentative d'inscription...</h3>";

// Inclure le fichier register.php
ob_start();
try {
    require_once '../../config/database.php';
    
    $data = $testData; // Utiliser directement les données de test
    
    echo "<p style='color: blue;'>✓ Données reçues</p>";
    
    // Validation basique
    $required = ['username','email','password','gender','birth_date','language'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            echo "<p style='color: red;'>✗ Champ requis manquant: $field</p>";
            exit;
        }
    }
    echo "<p style='color: blue;'>✓ Validation réussie</p>";
    
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: blue;'>✓ Connexion DB établie</p>";
    
    // Vérifier unicité
    $stmt = $db->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
    $stmt->execute([':u' => $data['username'], ':e' => $data['email']]);
    if ($stmt->fetch()) {
        echo "<p style='color: orange;'>⚠ Username/email déjà utilisé</p>";
        exit;
    }
    echo "<p style='color: blue;'>✓ Username/email disponible</p>";
    
    // Calculer l'âge
    $birth = new DateTime($data['birth_date']);
    $now = new DateTime();
    $age = (int)$now->format('Y') - (int)$birth->format('Y');
    echo "<p style='color: blue;'>✓ Âge calculé: $age ans</p>";
    
    // Hacher le mot de passe
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
    echo "<p style='color: blue;'>✓ Mot de passe haché</p>";
    
    // Valeurs par défaut
    $coins = 0; $diamonds = 3; $xp = 0; $level = 1; $current_stage = 1;
    
    // Détecter quelle colonne de mot de passe existe
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
    $passwordColumn = 'password';
    while ($row = $stmt->fetch()) {
        if ($row['Field'] === 'password_hash') {
            $passwordColumn = 'password_hash';
            break;
        }
    }
    echo "<p style='color: blue;'>✓ Colonne détectée: <strong>$passwordColumn</strong></p>";
    
    // Insérer
    $sql = "INSERT INTO users (username,email,{$passwordColumn},guide_name,guide_gender,age,level,xp,coins,diamonds,current_stage,created_at)
            VALUES (:username,:email,:password,:guide_name,:guide_gender,:age,:level,:xp,:coins,:diamonds,:current_stage,NOW())";
    
    echo "<p style='color: blue;'>✓ Requête SQL préparée</p>";
    echo "<pre>$sql</pre>";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':password' => $passwordHash,
        ':guide_name' => $data['guide_name'] ?? ($data['gender'] === 'boy' ? 'تيو' : 'ليا'),
        ':guide_gender' => $data['gender'],
        ':age' => $age,
        ':level' => $level,
        ':xp' => $xp,
        ':coins' => $coins,
        ':diamonds' => $diamonds,
        ':current_stage' => $current_stage
    ]);
    
    $userId = $db->lastInsertId();
    
    echo "<h3 style='color: green;'>✅ INSCRIPTION RÉUSSIE!</h3>";
    echo "<p>ID utilisateur créé: <strong>$userId</strong></p>";
    
    $user = [
        'id' => (int)$userId,
        'username' => $data['username'],
        'email' => $data['email'],
        'gender' => $data['gender'],
        'guide_name' => $data['guide_name'] ?? null,
        'level' => $level,
        'xp' => $xp,
        'coins' => $coins,
        'diamonds' => $diamonds,
        'current_stage' => $current_stage,
        'language' => $data['language']
    ];
    
    echo "<h3>Données utilisateur créées:</h3>";
    echo "<pre>" . json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERREUR</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

$output = ob_get_clean();
echo $output;
?>
