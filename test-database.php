<?php
// ==============================================
// SCRIPT DE TEST DE LA BASE DE DONNÉES
// ==============================================

require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <title>🧪 Test de la Base de Données</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0D0221 0%, #1A0033 100%);
            color: #fff;
            padding: 40px;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #FFD54F;
            margin-bottom: 30px;
        }
        .test-section {
            background: rgba(43, 10, 84, 0.8);
            border: 2px solid #9C27B0;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .test-item {
            margin: 15px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
        }
        .success {
            color: #26C281;
            font-weight: bold;
        }
        .error {
            color: #E91E63;
            font-weight: bold;
        }
        .info {
            color: #00BCD4;
        }
        code {
            background: rgba(0, 0, 0, 0.5);
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>🧪 اختبار قاعدة البيانات</h1>";

// Test 1: Connexion à la base de données
echo "<div class='test-section'>
    <h2>1️⃣ اختبار الاتصال بقاعدة البيانات</h2>";

if ($conn->ping()) {
    echo "<div class='test-item'><span class='success'>✅ الاتصال بقاعدة البيانات نجح!</span></div>";
} else {
    echo "<div class='test-item'><span class='error'>❌ فشل الاتصال: " . $conn->error . "</span></div>";
    exit;
}
echo "</div>";

// Test 2: التحقق من المجداول
echo "<div class='test-section'>
    <h2>2️⃣ التحقق من المجاول</h2>";

$tables = ['users', 'progress', 'rewards', 'qcm_answers', 'essays'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<div class='test-item'><span class='success'>✅ جدول $table موجود</span></div>";
    } else {
        echo "<div class='test-item'><span class='error'>❌ جدول $table غير موجود</span></div>";
    }
}
echo "</div>";

// Test 3: اختبار الإدراج والاستعلام
echo "<div class='test-section'>
    <h2>3️⃣ اختبار إدراج البيانات</h2>";

// إنشاء مستخدم اختبار
$testUsername = 'test_user_' . time();
$testEmail = 'test_' . time() . '@example.com';
$testPassword = password_hash('test123test', PASSWORD_BCRYPT);

$insertQuery = "INSERT INTO users (username, email, password, created_at) 
                VALUES ('$testUsername', '$testEmail', '$testPassword', NOW())";

if ($conn->query($insertQuery)) {
    $userId = $conn->insert_id;
    echo "<div class='test-item'><span class='success'>✅ تم إدراج المستخدم بنجاح (ID: $userId)</span></div>";
    
    // اختبار الاستعلام
    $selectQuery = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($selectQuery);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<div class='test-item'><span class='success'>✅ استعلام المستخدم نجح</span></div>";
        echo "<div class='test-item'><span class='info'>البيانات: " . htmlspecialchars(json_encode($user)) . "</span></div>";
    }
    
    // حذف المستخدم
    $deleteQuery = "DELETE FROM users WHERE id = $userId";
    if ($conn->query($deleteQuery)) {
        echo "<div class='test-item'><span class='success'>✅ تم حذف المستخدم الاختباري</span></div>";
    }
} else {
    echo "<div class='test-item'><span class='error'>❌ فشل الإدراج: " . $conn->error . "</span></div>";
}
echo "</div>";

// Test 4: ملخص قاعدة البيانات
echo "<div class='test-section'>
    <h2>4️⃣ ملخص قاعدة البيانات</h2>";

$tables_info = [
    'users' => 'عدد المستخدمين',
    'progress' => 'عدد سجلات التقدم',
    'rewards' => 'عدد سجلات المكافآت',
    'qcm_answers' => 'عدد إجابات QCM',
    'essays' => 'عدد المقالات'
];

foreach ($tables_info as $table => $label) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<div class='test-item'><span class='info'>📊 $label: " . $row['count'] . "</span></div>";
    }
}
echo "</div>";

// Test 5: المتغيرات الهامة
echo "<div class='test-section'>
    <h2>5️⃣ المتغيرات الهامة</h2>";

echo "<div class='test-item'><code>DB_HOST: " . htmlspecialchars(DB_HOST) . "</code></div>";
echo "<div class='test-item'><code>DB_NAME: " . htmlspecialchars(DB_NAME) . "</code></div>";
echo "<div class='test-item'><code>DB_USER: " . htmlspecialchars(DB_USER) . "</code></div>";
echo "<div class='test-item'><span class='success'>✅ جميع المتغيرات متوفرة</span></div>";
echo "</div>";

// Test 6: النصائح
echo "<div class='test-section'>
    <h2>6️⃣ الخطوات التالية</h2>";
echo "<div class='test-item'>
    <p><strong>للبدء باستخدام النظام:</strong></p>
    <ol>
        <li>انتقل إلى <code><a href='auth-login.php' style='color: #00BCD4;'>auth-login.php</a></code> لإنشاء حساب</li>
        <li>اقم بتسجيل الدخول</li>
        <li>انتقل إلى <code><a href='dashboard-stages.php' style='color: #00BCD4;'>dashboard-stages.php</a></code></li>
        <li>ابدأ اللعبة!</li>
    </ol>
</div>";
echo "</div>";

echo "
</div>
</body>
</html>";

$conn->close();
?>
