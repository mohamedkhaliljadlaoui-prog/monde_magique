<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Hugging Face API Configuration
// Prefer an environment variable over hardcoding tokens in source.
$apiKey = getenv('YOUR_HF_TOKEN
$model = 'mistralai/Mistral-7B-Instruct-v0.2';
$apiUrl = 'https://router.huggingface.co/v1/chat/completions';

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

if (!isset($input['message']) && !isset($input['question'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message requis']);
    exit;
}

$userMessage = trim((string)($input['message'] ?? $input['question'] ?? ''));
$context = is_array($input['context'] ?? null) ? $input['context'] : [];
$history = is_array($input['history'] ?? null) ? $input['history'] : (is_array($input['conversation_history'] ?? null) ? $input['conversation_history'] : []);

$language = strtolower(trim((string)($input['language'] ?? $context['language'] ?? '')));
if ($language !== 'ar' && $language !== 'fr') {
    $language = (preg_match('/[\x{0600}-\x{06FF}]/u', $userMessage) === 1) ? 'ar' : 'fr';
}

if ($userMessage === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Message requis']);
    exit;
}

if (!is_string($apiKey) || trim($apiKey) === '') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'عذراً 😔 خدمة المساعد غير مفعّلة حالياً.',
        'fallback' => true
    ]);
    exit;
}

$stageKey = (string)($context['stage_key'] ?? '');
$stageTitle = (string)($context['stage_title'] ?? $context['topic'] ?? '');
$stageId = (string)($context['stage'] ?? $context['stage_id'] ?? '');
$stationId = (string)($context['station'] ?? $context['station_id'] ?? '');

if ($language === 'ar') {
    $systemPrompt = "أنت مساعد تعليمي ودود للأطفال (8-12 سنة) داخل لعبة Monde Magique.\n"
        . "أجب بالعربية بشكل قصير وواضح ومشجع، وبحد أقصى 3-6 جمل.\n"
        . "إذا كان السؤال غير واضح، اسأل سؤال توضيحي واحد فقط.\n"
        . "تجنب أي محتوى مخيف أو غير مناسب للأطفال.\n"
        . "السياق: مرحلة={$stageId} مفتاح={$stageKey} موضوع=" . ($stageTitle !== '' ? $stageTitle : 'عام') . " محطة={$stationId}";
} else {
    $systemPrompt = "Tu es un assistant pédagogique bienveillant pour enfants (8-12 ans) dans le jeu Monde Magique.\n"
        . "Réponds en français simple (3-6 phrases), encourageant, avec un exemple concret.\n"
        . "Si la question est floue, pose une seule question de clarification.\n"
        . "Contexte: stage={$stageId} key={$stageKey} topic=" . ($stageTitle !== '' ? $stageTitle : 'Général') . " station={$stationId}";
}

$messages = [
    ['role' => 'system', 'content' => $systemPrompt],
];

foreach (array_slice($history, -8) as $turn) {
    if (!is_array($turn)) continue;
    if (isset($turn['role'], $turn['content']) && is_string($turn['role']) && is_string($turn['content'])) {
        $role = $turn['role'] === 'assistant' ? 'assistant' : ($turn['role'] === 'user' ? 'user' : null);
        $content = trim($turn['content']);
        if ($role && $content !== '') {
            $messages[] = ['role' => $role, 'content' => $content];
        }
        continue;
    }
    if (isset($turn['sender'], $turn['text']) && is_string($turn['sender']) && is_string($turn['text'])) {
        $role = $turn['sender'] === 'bot' ? 'assistant' : ($turn['sender'] === 'user' ? 'user' : null);
        $content = trim($turn['text']);
        if ($role && $content !== '') {
            $messages[] = ['role' => $role, 'content' => $content];
        }
        continue;
    }
}

// Use the OpenAI-compatible chat completions format via Hugging Face router.
$requestBody = [
    'model' => $model,
    'messages' => array_merge($messages, [
        ['role' => 'user', 'content' => $userMessage]
    ]),
    'max_tokens' => 256,
    'temperature' => 0.6,
    'stream' => false
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur de connexion',
        'details' => $error
    ]);
    exit;
}

if ($httpCode !== 200) {
    $parsedError = json_decode($response, true);
    $rawErrorMessage = null;
    if (is_array($parsedError)) {
        if (isset($parsedError['error']) && is_string($parsedError['error'])) {
            $rawErrorMessage = $parsedError['error'];
        } elseif (isset($parsedError['error']['message']) && is_string($parsedError['error']['message'])) {
            $rawErrorMessage = $parsedError['error']['message'];
        }
    }

    $userMessageOut = 'عذراً 😔 هناك مشكلة مؤقتة في الخدمة. جرّب بعد قليل.';
    if ($httpCode === 401 || $httpCode === 403) {
        $userMessageOut = 'عذراً 😔 خدمة المساعد غير مفعّلة حالياً.';
    } elseif ($httpCode === 429) {
        $userMessageOut = 'عذراً 😔 الخدمة مزدحمة الآن. جرّب بعد قليل.';
    }

    $payload = [
        'success' => true,
        'message' => $userMessageOut,
        'fallback' => true
    ];

    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        $payload['debug'] = [
            'upstream_http_code' => $httpCode,
            'upstream_error' => $rawErrorMessage,
            'model' => $model,
            'endpoint' => $apiUrl
        ];
    }

    http_response_code(200);
    echo json_encode($payload);
    exit;
}

$data = json_decode($response, true);

if (is_array($data) && isset($data['choices'][0]['message']['content'])) {
    $assistantMessage = trim((string)$data['choices'][0]['message']['content']);
    if ($assistantMessage !== '') {
        echo json_encode([
            'success' => true,
            'message' => $assistantMessage
        ]);
        exit;
    }
}

if (is_array($data) && isset($data['error'])) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'عذراً 😔 هناك مشكلة مؤقتة في الخدمة. جرّب بعد قليل.',
        'fallback' => true
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'مرحباً! 👋 كيف يمكنني مساعدتك في استكشاف العالم؟ 🌍',
    'fallback' => true
]);
?>
