<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Gemini API Configuration
$apiKey = 'AIzaSyD-QejGQNY1gII-h7CBUSY_ykPp2rOUnZY';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message requis']);
    exit;
}

$userMessage = $input['message'];

$systemPrompt = "أنت مساعد ودود ومتخصص في تطبيق Monde Magique للأطفال. أجب بالعربية بشكل قصير، واضح، ومشجع. استخدم الرموز التعبيرية المناسبة. تجنب الإجابات المخيفة أو غير المناسبة للأطفال.";

$fullPrompt = $systemPrompt . "\n\nسؤال المستخدم: " . $userMessage;

$requestBody = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $fullPrompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.6,
        'maxOutputTokens' => 256
    ]
];

$ch = curl_init($apiUrl . '?key=' . urlencode($apiKey));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

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

if ($httpCode === 429) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'عذراً 😔 تم تجاوز الحد المجاني مؤقتاً. انتظر قليلاً وحاول مرة أخرى، أو فعّل خطة مجانية/فواتير في Google AI Studio.',
        'fallback' => true
    ]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode([
        'error' => 'Erreur API Gemini',
        'code' => $httpCode,
        'response' => $response
    ]);
    exit;
}

$data = json_decode($response, true);

if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    echo json_encode([
        'success' => true,
        'message' => trim($data['candidates'][0]['content']['parts'][0]['text'])
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
