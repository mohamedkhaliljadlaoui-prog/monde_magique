<?php
// ملف اختبار Groq API
header('Content-Type: application/json; charset=utf-8');

define('GROQ_API_KEY', 'YOUR_API_KEY');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

$debug = [
    'api_key_length' => strlen(GROQ_API_KEY),
    'api_key_starts_with' => substr(GROQ_API_KEY, 0, 10) . '...',
    'timestamp' => date('Y-m-d H:i:s'),
    'models_to_test' => ['llama-3.1-70b-versatile', 'mixtral-8x7b-32768', 'llama-2-70b-chat']
];

// اختبر الاتصال الأساسي
$testMessage = "مرحباً";

$requestBody = [
    'model' => 'llama-3.1-70b-versatile',
    'messages' => [
        ['role' => 'user', 'content' => $testMessage]
    ],
    'max_tokens' => 100
];

$ch = curl_init(GROQ_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . GROQ_API_KEY,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$debug['http_code'] = $httpCode;
$debug['curl_error'] = $error;
$debug['response_preview'] = substr($response, 0, 200);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $debug['success'] = true;
    $debug['message'] = $data['choices'][0]['message']['content'] ?? 'لا توجد رسالة';
} else {
    $debug['success'] = false;
    $debug['full_response'] = json_decode($response, true);
}

echo json_encode($debug, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
