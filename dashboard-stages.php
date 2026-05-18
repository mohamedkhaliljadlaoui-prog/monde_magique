<?php
// ==============================================
// DASHBOARD AVEC BASE DE DONNÉES
// ==============================================

require_once 'config.php';
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Récupérer les données du utilisateur
$rewards = $conn->query("SELECT * FROM rewards WHERE user_id=$user_id")->fetch_assoc();
$progress = [];
$result = $conn->query("SELECT * FROM progress WHERE user_id=$user_id ORDER BY stage_num");
while ($row = $result->fetch_assoc()) {
    $progress[$row['stage_num']] = $row;
}

// Compter les stages complétés
$completed_count = 0;
for ($i = 1; $i <= 10; $i++) {
    if (isset($progress[$i]) && $progress[$i]['completed']) {
        $completed_count++;
    }
}

// Coder les données pour le JavaScript
$progress_json = json_encode($progress, JSON_UNESCAPED_UNICODE);
$rewards_json = json_encode($rewards, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 منصة معالم ماجيك - لعبة الجغرافيا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--candy-red:#E91E63;--candy-orange:#FF9800;--candy-yellow:#FFD54F;--candy-green:#26C281;--candy-teal:#00BCD4;--candy-blue:#2196F3;--candy-purple:#9C27B0;--candy-pink:#EC407A;--bg-deep:#0D0221;--bg-mid:#1A0033;--bg-card:#2B0A54;--text-light:#FFFFFF;--text-muted:rgba(255,255,255,0.65)}
        *{margin:0;padding:0;box-sizing:border-box}html,body{height:100%;font-family:'Nunito',sans-serif;background:linear-gradient(135deg,var(--bg-deep) 0%,var(--bg-mid) 100%);color:var(--text-light);overflow-x:hidden}
        body::before{content:'';position:fixed;inset:0;background:radial-gradient(circle at 15% 20%,#FF336622 0%,transparent 40%),radial-gradient(circle at 85% 70%,#00AAFF22 0%,transparent 40%);pointer-events:none;z-index:0}
        .container{max-width:1400px;margin:0 auto;padding:40px 20px;position:relative;z-index:1}
        .header{text-align:center;margin-bottom:60px;text-shadow:0 2px 8px rgba(0,0,0,0.3)}
        .header h1{font-size:3rem;color:var(--candy-yellow);margin-bottom:10px;animation:slideIn 0.6s ease-out;text-shadow:0 4px 15px rgba(255,215,0,0.3)}
        .header .icon{font-size:4rem;display:inline-block;margin:0 10px;animation:bounce 1s ease-in-out infinite}
        .header p{font-size:1.3rem;color:var(--text-muted);margin-top:10px;animation:slideIn 0.8s ease-out}
        .user-info{text-align:center;margin-bottom:30px;color:var(--candy-yellow);font-size:1.1rem}
        .logout-btn{background:linear-gradient(135deg,var(--candy-red),var(--candy-orange));color:white;border:none;padding:10px 20px;border-radius:20px;cursor:pointer;font-weight:700;margin-left:20px;transition:all 0.3s}
        .logout-btn:hover{transform:translateY(-2px);box-shadow:0 4px 15px rgba(233,30,99,0.4)}
        .progress-bar-container{margin:40px 0;background:rgba(156,39,176,0.15);border-radius:50px;overflow:hidden;height:40px;border:3px solid var(--candy-purple)}
        .progress-bar{background:linear-gradient(90deg,var(--candy-purple),var(--candy-blue),var(--candy-teal));height:100%;transition:width 0.6s ease-out;width:0%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem}
        .stats{display:flex;justify-content:center;gap:40px;margin:40px 0;flex-wrap:wrap}
        .stat-card{background:linear-gradient(135deg,var(--bg-card) 0%,var(--bg-mid) 100%);padding:25px 40px;border-radius:15px;border:2px solid var(--candy-yellow);text-align:center;box-shadow:0 4px 20px rgba(156,39,176,0.3);animation:scaleIn 0.5s ease-out;transition:all 0.3s}
        .stat-card:hover{transform:translateY(-5px);box-shadow:0 8px 30px rgba(156,39,176,0.5)}
        .stat-card .value{font-size:2.5rem;color:var(--candy-yellow);font-weight:700;margin-bottom:10px}
        .stat-card .label{color:var(--text-muted);font-size:1rem}
        .stages-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:25px;margin-top:50px;animation:slideIn 1s ease-out}
        .stage-card{background:linear-gradient(135deg,var(--bg-card) 0%,var(--bg-mid) 100%);border-radius:20px;padding:30px;border:3px solid transparent;cursor:pointer;transition:all 0.3s;position:relative;overflow:hidden;box-shadow:0 8px 30px rgba(156,39,176,0.2);animation:slideUp 0.5s ease-out}
        .stage-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(156,39,176,0.2),rgba(0,188,212,0.2));opacity:0;transition:opacity 0.3s}
        .stage-card:hover::before{opacity:1}
        .stage-card:hover{transform:translateY(-10px);border-color:var(--candy-yellow);box-shadow:0 12px 40px rgba(156,39,176,0.4)}
        .stage-card.locked{opacity:0.6;cursor:not-allowed}
        .stage-card.locked:hover{transform:none;border-color:transparent;box-shadow:0 8px 30px rgba(156,39,176,0.2)}
        .stage-card .icon{font-size:4rem;margin-bottom:15px;display:block;text-align:center;animation:scaleIn 0.5s ease-out}
        .stage-card h3{font-size:1.8rem;color:var(--candy-yellow);margin-bottom:10px;text-align:center}
        .stage-card .subtitle{color:var(--text-muted);text-align:center;font-size:0.95rem;margin-bottom:20px}
        .stage-card .progress{background:rgba(156,39,176,0.2);border-radius:10px;height:8px;margin:15px 0;overflow:hidden}
        .stage-card .progress-fill{background:var(--candy-green);height:100%;width:0%;transition:width 0.3s}
        .stage-card.completed .progress-fill{width:100%}
        .stage-card .status{font-size:0.9rem;text-align:center;margin-top:15px;padding:10px;background:rgba(0,188,212,0.15);border-radius:8px;color:var(--text-light)}
        .stage-card.completed .status{background:rgba(38,194,129,0.15);color:var(--candy-green)}
        .stage-card.locked .status{background:rgba(233,30,99,0.15);color:var(--candy-red)}
        .btn-play{background:linear-gradient(135deg,var(--candy-blue),var(--candy-teal));color:white;border:none;padding:12px 25px;border-radius:25px;font-weight:700;cursor:pointer;margin-top:15px;width:100%;transition:all 0.3s;font-size:1.1rem;display:flex;align-items:center;justify-content:center;gap:10px}
        .btn-play:hover:not(:disabled){transform:scale(1.05);box-shadow:0 4px 15px rgba(0,188,212,0.4)}
        .btn-play:disabled{opacity:0.5;cursor:not-allowed}
        .lock-icon{position:absolute;top:15px;right:15px;font-size:2rem;color:var(--candy-red);display:none}
        .stage-card.locked .lock-icon{display:block}
        .check-icon{position:absolute;top:15px;left:15px;font-size:2rem;color:var(--candy-green);display:none}
        .stage-card.completed .check-icon{display:block}
        @keyframes slideIn{from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:translateX(0)}}
        @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes scaleIn{from{opacity:0;transform:scale(0.8)}to{opacity:1;transform:scale(1)}}
        @keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        .total-score{text-align:center;margin:40px 0;padding:30px;background:linear-gradient(135deg,rgba(38,194,129,0.15),rgba(76,175,80,0.1));border-radius:20px;border:3px solid var(--candy-green);animation:slideUp 0.6s ease-out}
        .total-score h2{color:var(--candy-green);font-size:2rem;margin-bottom:15px}
        .total-score .rewards{display:flex;justify-content:center;gap:30px;flex-wrap:wrap;margin-top:20px}
        .total-score .reward-item{font-size:1.8rem;font-weight:700}
        .total-score .reward-item span{color:var(--candy-yellow)}
        @media(max-width:768px){.header h1{font-size:2rem}.header .icon{font-size:2.5rem}.stages-grid{grid-template-columns:1fr}.stat-card{padding:15px 25px}.stage-card{padding:20px}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="icon">🎮</span>منصة معالم ماجيك<span class="icon">🌍</span></h1>
            <p>🚀 استكشف <span style="color:var(--candy-yellow);font-weight:700">10 مراحل</span> رائعة وتعلم جغرافية تونس والعالم!</p>
            <div class="user-info">
                مرحباً بك: <strong id="username-display"><?php echo htmlspecialchars($username); ?></strong>
                <button class="logout-btn" onclick="logout()">تسجيل الخروج</button>
            </div>
        </div>
        
        <div class="progress-bar-container">
            <div class="progress-bar" id="progressBar">0% ✓</div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="value" id="stagesCompletedCount"><?php echo $completed_count; ?></div>
                <div class="label">المراحل المكملة</div>
            </div>
            <div class="stat-card">
                <div class="value" id="totalDiamonds"><?php echo intval($rewards['total_diamonds'] ?? 0); ?></div>
                <div class="label">💎 إجمالي الألماس</div>
            </div>
            <div class="stat-card">
                <div class="value" id="totalCoins"><?php echo intval($rewards['total_coins'] ?? 0); ?></div>
                <div class="label">🪙 إجمالي العملات</div>
            </div>
        </div>

        <div class="stages-grid" id="stagesContainer"></div>
        
        <div class="total-score">
            <h2>🏆 إجمالي النقاط</h2>
            <div class="rewards">
                <div class="reward-item">⭐ <span id="totalStars"><?php echo $completed_count; ?></span> نجمة</div>
                <div class="reward-item">💎 <span id="totalRewardDiamonds"><?php echo intval($rewards['total_diamonds'] ?? 0); ?></span></div>
                <div class="reward-item">🪙 <span id="totalRewardCoins"><?php echo intval($rewards['total_coins'] ?? 0); ?></span></div>
            </div>
        </div>
    </div>

    <script>
        // ===== DONNÉES DEPUIS PHP =====
        const userProgress = <?php echo $progress_json; ?>;
        const userRewards = <?php echo $rewards_json; ?>;
        const userId = <?php echo $user_id; ?>;
        
        const stages = [
            {num:1,icon:'🗺️',title:'المجال الجغرافي',subtitle:'اكتشف المفاهيم الأساسية للمجالات الجغرافية والطبيعية'},
            {num:2,icon:'🗺️',title:'المجال الجغرافي للمغرب العربي',subtitle:'اكتشف خصائص المجال الجغرافي للمغرب العربي'},
            {num:3,icon:'🌍',title:'المجال الجغرافي للبلاد التونسية',subtitle:'اكتشف التفاصيل الجغرافية لتونس'},
            {num:4,icon:'🏘️',title:'المشهد الريفي المحلي',subtitle:'اكتشف خصائص المشهد الريفي والبيئة الريفية'},
            {num:5,icon:'🏙️',title:'المشهد الحضري',subtitle:'اكتشف خصائص المدن والمشاهد الحضرية'},
            {num:6,icon:'🔄',title:'العلاقة بين الريف و المدينة',subtitle:'اكتشف التفاعل بين المناطق الريفية والحضرية'},
            {num:7,icon:'👥',title:'البلاد التونسية:التوزع الجغرافي للسكان',subtitle:'اكتشف توزيع السكان في تونس'},
            {num:8,icon:'🌾',title:'التوزّع الفلاحي للبلاد التونسية',subtitle:'اكتشف التوزيع الجغرافي للنشاط الفلاحي'},
            {num:9,icon:'🏭',title:'الصناعة في البلاد التونسية',subtitle:'اكتشف أهمية الصناعة والتوزيع الصناعي في تونس'},
            {num:10,icon:'🤝',title:'التجارة الخارجية التونسية',subtitle:'اكتشف الصادرات والواردات والميزان التجاري'}
        ];

        function renderStages(){
            const container=document.getElementById('stagesContainer');
            container.innerHTML='';
            const isStageCompleted = (n) => {
                const v = userProgress?.[n]?.completed;
                return v === true || v === 1 || v === '1';
            };
            stages.forEach((s,i)=>{
                const isCompleted = isStageCompleted(s.num);
                const prevCompleted = (s.num === 1) ? true : isStageCompleted(s.num - 1);
                const isLocked = (!isCompleted && !prevCompleted);
                const card=document.createElement('div');
                card.className='stage-card'+(isCompleted?' completed':'')+(isLocked?' locked':'');
                card.innerHTML=`
                    <div class="check-icon">✓</div>
                    <div class="lock-icon">🔒</div>
                    <span class="icon">${s.icon}</span>
                    <h3>#${s.num} ${s.title}</h3>
                    <p class="subtitle">${s.subtitle}</p>
                    <div class="progress"><div class="progress-fill" style="width:${isCompleted?'100':'0'}%"></div></div>
                    <div class="status">${isCompleted?'✓ مكتمل':isLocked?'🔒 مقفل':'🔓 متاح'}</div>
                    <button class="btn-play" onclick="playStage(${s.num})" ${isLocked?'disabled':''}>${isLocked?'🔒':'▶️'} ${isLocked?'مقفل':'ابدأ'}</button>
                `;
                container.appendChild(card);
            });
            updateProgress();
        }

        function playStage(n){
            window.location.href='stage.php?stage='+n;
        }

        function updateProgress(){
            const completed=Object.values(userProgress).filter(p=>p?.completed).length;
            const percentage=Math.round(completed/stages.length*100);
            document.getElementById('progressBar').style.width=percentage+'%';
            document.getElementById('progressBar').textContent=percentage+'% ✓';
        }

        function logout(){
            fetch('auth.php?action=logout',{credentials:'include'})
                .then(()=>window.location.href='index.html');
        }

        // ===== WELCOME VOICE (AR) =====
        function playWelcomeVoiceOnce() {
            const KEY = 'mm_welcome_voice_v3';
            if (sessionStorage.getItem(KEY) === '1') return;
            if (!('speechSynthesis' in window)) return;

            const synth = window.speechSynthesis;
            const text = 'مرحبًا بكم يا مستكشفي الأرض الصغار، انطلقوا معنا في رحلة شيّقة عبر كوكب الجغرافيا حيث تتحوّل الخرائط إلى مغامرات، وتصبح كلّ قارة حكاية تنتظر من يكتشفها!';

            const unlockAudio = async () => {
                try {
                    const Ctx = window.AudioContext || window.webkitAudioContext;
                    if (!Ctx) return;
                    const ctx = new Ctx();
                    if (ctx.state === 'suspended') {
                        await ctx.resume();
                    }
                } catch (_) {
                    // ignore
                }
            };

            let done = false;
            let speaking = false;

            const speakNow = (lang = 'ar-SA', allowCancel = false) => {
                if (done || speaking) return;
                try {
                    if (allowCancel) {
                        try { synth.cancel(); } catch (_) {}
                    }

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = lang;
                    utterance.rate = 1;
                    utterance.pitch = 1.2;
                    utterance.volume = 1;

                    const voices = synth.getVoices();
                    const arVoice = voices.find(v => (v.lang || '').toLowerCase().startsWith('ar'));
                    if (arVoice) utterance.voice = arVoice;

                    utterance.onstart = () => {
                        speaking = true;
                    };
                    utterance.onend = () => {
                        speaking = false;
                        done = true;
                        sessionStorage.setItem(KEY, '1');
                    };
                    utterance.onerror = () => {
                        speaking = false;
                    };

                    synth.speak(utterance);

                    // If it didn't start (common when blocked), retry once with a generic lang.
                    setTimeout(() => {
                        if (!done && !speaking && !synth.speaking) {
                            try { synth.speak(new SpeechSynthesisUtterance('')); } catch (_) {}
                            speakNow('ar', false);
                        }
                    }, 800);
                } catch (_) {
                    // ignore
                }
            };

            // Speak even if voices list is empty (common on some browsers).
            const maybeSpeak = () => {
                if (done || speaking) return;
                speakNow('ar-SA', false);
            };

            if (typeof synth.onvoiceschanged !== 'undefined') {
                const prev = synth.onvoiceschanged;
                synth.onvoiceschanged = () => {
                    try { if (typeof prev === 'function') prev(); } catch (_) {}
                    maybeSpeak();
                };
            }

            // Attempt shortly after load
            setTimeout(() => {
                maybeSpeak();
                // Extra retry (some browsers need a bit longer)
                setTimeout(() => maybeSpeak(), 1200);
            }, 300);

            // Fallback: speak on first user interaction
            const onFirstInteraction = () => {
                unlockAudio();
                try { synth.resume(); } catch (_) {}
                // On interaction, it's safe to cancel then restart.
                speakNow('ar-SA', true);
            };
            document.addEventListener('pointerdown', onFirstInteraction, { once: true });
            document.addEventListener('keydown', onFirstInteraction, { once: true });
        }

        window.addEventListener('DOMContentLoaded',()=>{
            renderStages();
            playWelcomeVoiceOnce();
        });

        // Recharger quand la fenêtre reprend le focus
        window.addEventListener('focus',()=>{
            location.reload();
        });
    </script>
</body>
</html>
