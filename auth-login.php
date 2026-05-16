<?php
// ==============================================
// PAGE DE CONNEXION/INSCRIPTION AVEC BASE DE DONNÉES
// ==============================================

require_once 'config.php';
session_start();

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard-stages.php');
    exit;
}

// Traiter les requêtes AJAX
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $response = ['success' => false, 'message' => ''];
    
    if ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'تعبئة جميع الحقول مطلوبة';
        } elseif (strlen($password) < 6) {
            $response['message'] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
        } elseif ($password !== $confirm_password) {
            $response['message'] = 'كلمات المرور غير متطابقة';
        } else {
            // التحقق من عدم تكرار المستخدم
            $check_user = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email'");
            if ($check_user->num_rows > 0) {
                $response['message'] = 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل';
            } else {
                // تشفير كلمة المرور
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // إنشاء حساب جديد
                $insert = $conn->query("INSERT INTO users (username, email, password, created_at) VALUES ('$username', '$email', '$hashed_password', NOW())");
                
                if ($insert) {
                    $user_id = $conn->insert_id;
                    
                    // إنشاء مدخلات التقدم لكل مرحلة
                    for ($i = 1; $i <= 10; $i++) {
                        $conn->query("INSERT INTO progress (user_id, stage_num, completed, qcm_score, essay_score, diamonds, coins, current_step, updated_at) VALUES ($user_id, $i, 0, 0, 0, 0, 0, 0, NOW())");
                    }
                    
                    // إنشاء مدخل المكافآت
                    $conn->query("INSERT INTO rewards (user_id, total_diamonds, total_coins, total_stages_completed) VALUES ($user_id, 0, 0, 0)");
                    
                    // تسجيل الدخول تلقائياً
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    
                    $response['success'] = true;
                    $response['message'] = 'تم إنشاء الحساب بنجاح!';
                    $response['redirect'] = 'dashboard-stages.php';
                } else {
                    $response['message'] = 'خطأ في إنشاء الحساب: ' . $conn->error;
                }
            }
        }
    } 
    elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $response['message'] = 'البريد الإلكتروني وكلمة المرور مطلوبة';
        } else {
            $result = $conn->query("SELECT id, username, password FROM users WHERE email='$email'");
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // تحديث آخر تسجيل دخول
                    $conn->query("UPDATE users SET last_login=NOW() WHERE id={$user['id']}");
                    
                    $response['success'] = true;
                    $response['message'] = 'تم تسجيل الدخول بنجاح!';
                    $response['redirect'] = 'dashboard-stages.php';
                } else {
                    $response['message'] = 'كلمة المرور غير صحيحة';
                }
            } else {
                $response['message'] = 'المستخدم غير موجود';
            }
        }
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// إذا كانت طلب GET، عرض صفحة HTML
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 منصة معالم ماجيك - تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --candy-red: #E91E63;
            --candy-orange: #FF9800;
            --candy-yellow: #FFD54F;
            --candy-green: #26C281;
            --candy-teal: #00BCD4;
            --candy-blue: #2196F3;
            --candy-purple: #9C27B0;
            --candy-pink: #EC407A;
            --bg-deep: #0D0221;
            --bg-mid: #1A0033;
            --bg-card: #2B0A54;
            --text-light: #FFFFFF;
            --text-muted: rgba(255,255,255,0.65);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, var(--bg-deep) 0%, var(--bg-mid) 100%);
            color: var(--text-light);
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 15% 20%, #FF336622 0%, transparent 40%),
                        radial-gradient(circle at 85% 70%, #00AAFF22 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }
        
        .container {
            display: flex;
            height: 100%;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .form-container {
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-mid) 100%);
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            border: 3px solid var(--candy-purple);
            box-shadow: 0 12px 40px rgba(156, 39, 176, 0.3);
            animation: slideIn 0.6s ease-out;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header .icon {
            font-size: 3rem;
            display: inline-block;
            margin-bottom: 15px;
            animation: bounce 1s ease-in-out infinite;
        }
        
        .header h1 {
            font-size: 2rem;
            color: var(--candy-yellow);
            margin-bottom: 10px;
            text-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--candy-yellow);
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--candy-purple);
            border-radius: 10px;
            background: rgba(156, 39, 176, 0.1);
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        input::placeholder {
            color: var(--text-muted);
        }
        
        input:focus {
            outline: none;
            border-color: var(--candy-teal);
            background: rgba(0, 188, 212, 0.1);
            box-shadow: 0 4px 15px rgba(0, 188, 212, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--candy-purple), var(--candy-blue));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 39, 176, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--candy-teal);
            border: 2px solid var(--candy-teal);
        }
        
        .btn-secondary:hover {
            background: rgba(0, 188, 212, 0.1);
            transform: translateY(-2px);
        }
        
        .toggle-form {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(156, 39, 176, 0.3);
        }
        
        .toggle-form p {
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        
        .toggle-form button {
            color: var(--candy-yellow);
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .toggle-form button:hover {
            color: var(--candy-teal);
            transform: scale(1.05);
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            animation: slideIn 0.4s ease-out;
        }
        
        .message.success {
            background: rgba(38, 194, 129, 0.2);
            color: var(--candy-green);
            border: 2px solid var(--candy-green);
        }
        
        .message.error {
            background: rgba(233, 30, 99, 0.2);
            color: var(--candy-red);
            border: 2px solid var(--candy-red);
        }
        
        .form {
            display: none;
        }
        
        .form.active {
            display: block;
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .loading {
            display: none;
        }
        
        .loading.active {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @media(max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .header .icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="header">
                <div class="icon">🎮</div>
                <h1>منصة معالم ماجيك</h1>
                <p>🌍 استكشف العالم وتعلم الجغرافيا</p>
            </div>
            
            <div id="message"></div>
            
            <!-- نموذج تسجيل الدخول -->
            <form class="form active" id="loginForm">
                <div class="form-group">
                    <label for="loginEmail">📧 البريد الإلكتروني</label>
                    <input type="email" id="loginEmail" placeholder="your@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="loginPassword">🔐 كلمة المرور</label>
                    <input type="password" id="loginPassword" placeholder="••••••" required>
                </div>
                
                <button type="button" class="btn btn-primary" onclick="handleLogin()">
                    <span id="loginBtnText">تسجيل الدخول</span>
                    <i class="fas fa-spinner loading" id="loginSpinner"></i>
                </button>
                
                <div class="toggle-form">
                    <p>ليس لديك حساب؟</p>
                    <button type="button" onclick="toggleForm()">إنشاء حساب جديد</button>
                </div>
            </form>
            
            <!-- نموذج الإنشاء الجديد -->
            <form class="form" id="registerForm">
                <div class="form-group">
                    <label for="registerUsername">👤 اسم المستخدم</label>
                    <input type="text" id="registerUsername" placeholder="your_username" required>
                </div>
                
                <div class="form-group">
                    <label for="registerEmail">📧 البريد الإلكتروني</label>
                    <input type="email" id="registerEmail" placeholder="your@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="registerPassword">🔐 كلمة المرور</label>
                    <input type="password" id="registerPassword" placeholder="••••••" required>
                </div>
                
                <div class="form-group">
                    <label for="registerConfirmPassword">🔐 تأكيد كلمة المرور</label>
                    <input type="password" id="registerConfirmPassword" placeholder="••••••" required>
                </div>
                
                <button type="button" class="btn btn-primary" onclick="handleRegister()">
                    <span id="registerBtnText">إنشاء الحساب</span>
                    <i class="fas fa-spinner loading" id="registerSpinner"></i>
                </button>
                
                <div class="toggle-form">
                    <p>هل لديك حساب بالفعل؟</p>
                    <button type="button" onclick="toggleForm()">تسجيل الدخول</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleForm() {
            document.getElementById('loginForm').classList.toggle('active');
            document.getElementById('registerForm').classList.toggle('active');
            document.getElementById('message').innerHTML = '';
        }
        
        function showMessage(text, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.className = 'message ' + type;
            msgDiv.innerHTML = text;
        }
        
        function handleLogin() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!email || !password) {
                showMessage('⚠️ تعبئة جميع الحقول مطلوبة', 'error');
                return;
            }
            
            const spinner = document.getElementById('loginSpinner');
            const btnText = document.getElementById('loginBtnText');
            spinner.classList.add('active');
            btnText.textContent = 'جاري التحميل...';
            
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);
            
            fetch('auth-login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                spinner.classList.remove('active');
                btnText.textContent = 'تسجيل الدخول';
                
                if (data.success) {
                    showMessage('✅ ' + data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);
                } else {
                    showMessage('❌ ' + data.message, 'error');
                }
            })
            .catch(err => {
                spinner.classList.remove('active');
                btnText.textContent = 'تسجيل الدخول';
                showMessage('❌ خطأ في الاتصال: ' + err.message, 'error');
            });
        }
        
        function handleRegister() {
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            
            if (!username || !email || !password || !confirmPassword) {
                showMessage('⚠️ تعبئة جميع الحقول مطلوبة', 'error');
                return;
            }
            
            const spinner = document.getElementById('registerSpinner');
            const btnText = document.getElementById('registerBtnText');
            spinner.classList.add('active');
            btnText.textContent = 'جاري الإنشاء...';
            
            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('confirm_password', confirmPassword);
            
            fetch('auth-login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                spinner.classList.remove('active');
                btnText.textContent = 'إنشاء الحساب';
                
                if (data.success) {
                    showMessage('✅ ' + data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);
                } else {
                    showMessage('❌ ' + data.message, 'error');
                }
            })
            .catch(err => {
                spinner.classList.remove('active');
                btnText.textContent = 'إنشاء الحساب';
                showMessage('❌ خطأ في الاتصال: ' + err.message, 'error');
            });
        }
    </script>
</body>
</html>
