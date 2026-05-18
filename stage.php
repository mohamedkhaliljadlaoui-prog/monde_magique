<?php
// ==============================================
// STAGE WRAPPER - Sert les stages avec l'API
// ==============================================

require_once 'config.php';
session_start();

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: auth-login.php');
    exit;
}

// Récupérer le numéro de stage depuis l'URL
$stageNum = isset($_GET['stage']) ? intval($_GET['stage']) : 1;

if ($stageNum < 1 || $stageNum > 10) {
    header('Location: dashboard-stages.php');
    exit;
}

// Bloquer l'accès si l'étape précédente n'est pas complétée
$userId = (int)$_SESSION['user_id'];
if ($stageNum > 1) {
    $prevStage = $stageNum - 1;
    $prevResult = $conn->query("SELECT completed FROM progress WHERE user_id=$userId AND stage_num=$prevStage LIMIT 1");
    $prev = $prevResult ? $prevResult->fetch_assoc() : null;
    $prevCompleted = $prev && intval($prev['completed'] ?? 0) === 1;
    if (!$prevCompleted) {
        header('Location: dashboard-stages.php');
        exit;
    }
}

// Mapper les stages aux fichiers HTML
$stageFiles = [
    1 => 'stage-1-tunisia.html',
    2 => 'stage-2-maghreb.html',
    3 => 'stage-3-africa.html',
    4 => 'stage-4-europe.html',
    5 => 'stage-5-asia.html',
    6 => 'stage-6-namerica.html',
    7 => 'stage-7-samerica.html',
    8 => 'stage-8-oceania.html',
    9 => 'stage-9-poles.html',
    10 => 'stage-10-world.html'
];

$stageFile = $stageFiles[$stageNum] ?? null;

if (!$stageFile || !file_exists($stageFile)) {
    header('Location: dashboard-stages.php');
    exit;
}

// Lire le fichier HTML
$htmlContent = file_get_contents($stageFile);

// Récupérer les données actuelles du stage
$progressResult = $conn->query("SELECT * FROM progress WHERE user_id={$_SESSION['user_id']} AND stage_num=$stageNum");
$progress = $progressResult->fetch_assoc();

// Récupérer les récompenses
$rewardsResult = $conn->query("SELECT * FROM rewards WHERE user_id={$_SESSION['user_id']}");
$rewards = $rewardsResult->fetch_assoc();

// Préparer les données JSON
$gameState = json_encode([
    'stageNum' => $stageNum,
    'currentStep' => $progress['last_step'] ?? 1,
    'qcmScore' => $progress['qcm_score'] ?? 0,
    'diamonds' => $rewards['total_diamonds'] ?? 0,
    'coins' => $rewards['total_coins'] ?? 0,
    'userId' => $_SESSION['user_id'],
    'username' => $_SESSION['username']
], JSON_UNESCAPED_UNICODE);

// Injecter le script API avant la fin du body
$apiInjection = <<<'SCRIPT'
<!-- API Manager -->
<script src="api-manager.js"></script>
<script>
// Initialiser le manager
apiManager.setStageNum(STAGE_NUM_PLACEHOLDER);

// Attendre la session et charger les données
waitForSession().then(async () => {
    // Récupérer les données sauvegardées
    const savedProgress = await apiManager.loadProgress();
    
    // Restaurer l'état si disponible
    if (savedProgress && savedProgress.current_step) {
        currentStep = Math.min(savedProgress.current_step, totalSteps);
    }
    
    // Restaurer les ressources
    if (savedProgress) {
        gameState.diamonds = savedProgress.diamonds || 0;
        gameState.coins = savedProgress.coins || 0;
        gameState.qcmScore = savedProgress.qcm_score || 0;
    }
    
    updateResources();
    updateUI();
});

// Redéfinir le stockage des données
const originalNextStep = nextStep;
nextStep = function() {
    originalNextStep.call(this);
    
    // Sauvegarder la progression avec l'API
    if (typeof apiManager !== 'undefined') {
        apiManager.saveProgress({
            current_step: currentStep,
            qcm_score: gameState.qcmScore,
            diamonds: gameState.diamonds,
            coins: gameState.coins
        });
    }
};

// Surcharger la sauvegarde du QCM
const originalNextStepQCM = nextStep;
const originalEvaluateEssay = evaluateEssay;
evaluateEssay = async function() {
    // Appeler l'original
    originalEvaluateEssay.call(this);
    
    // Sauvegarder via API
    if (typeof apiManager !== 'undefined') {
        await apiManager.saveQCM(selectedAnswers, gameState.qcmScore, true);
    }
};

// Surcharger la fin du stage
const originalSetupFinalButton = () => {
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.onclick = async function() {
            // Sauvegarder comme complété
            if (typeof apiManager !== 'undefined') {
                const essayText = document.querySelector('textarea')?.value || '';
                const wordCount = essayText.split(/\s+/).filter(w => w).length;
                const score = Math.min(100, Math.max(50, wordCount * 2));
                
                await apiManager.saveEssay(essayText, wordCount, score);
                const result = await apiManager.completeStage(gameState.qcmScore);
                
                // Si le stage suivant existe, aller le jouer
                if (result && result.success && result.next_stage) {
                    apiManager.goToNextStage(result.next_stage);
                } else {
                    // Sinon retourner au dashboard
                    apiManager.goToDashboard();
                }
            }
        };
    }
};

setTimeout(originalSetupFinalButton, 500);
</script>
SCRIPT;

$apiInjection = str_replace('STAGE_NUM_PLACEHOLDER', $stageNum, $apiInjection);
$htmlContent = str_replace('</body>', $apiInjection . '</body>', $htmlContent);

// Ajouter un bouton pour retourner au dashboard
$dashboardButton = <<<'HTML'
<div style="position: fixed; top: 20px; left: 20px; z-index: 1000;">
    <button onclick="apiManager?.goToDashboard()" style="
        background: linear-gradient(135deg, #2196F3, #00BCD4);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 25px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.3)'">
        ← العودة للوحة التحكم
    </button>
</div>
HTML;

$htmlContent = str_replace('<body>', '<body>' . $dashboardButton, $htmlContent);

// Afficher le contenu modifié
echo $htmlContent;
?>
