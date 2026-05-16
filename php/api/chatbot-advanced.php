<?php
// ===================================
// CHATBOT-ADVANCED.PHP - Chatbot IA Avancé
// Utilise plusieurs modèles IA puissants (Groq, Mistral, etc.)
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

class AdvancedChatbotAPI {
    private $db;
    private $conversationMemory = [];

    // Configuration des APIs
    private $apis = [
        'groq' => [
            'enabled' => true,
            'key' => 'VOTRE_CLE_GROQ_ICI', // À obtenir sur https://console.groq.com
            'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
            'models' => [
                'llama3-70b-8192',      // Le plus puissant
                'llama3-8b-8192',       // Rapide
                'mixtral-8x7b-32768',   // Très intelligent
                'gemma-7b-it'           // Google
            ],
            'priority' => 1
        ],
        'huggingface' => [
            'enabled' => true,
            'key' => 'YOUR_YOUR_HF_TOKEN
            'endpoint' => 'https://api-inference.huggingface.co/models/',
            'models' => [
                'mistralai/Mistral-7B-Instruct-v0.2',
                'microsoft/phi-2',
                'facebook/blenderbot-400M-distill'
            ],
            'priority' => 2
        ],
        'together' => [
            'enabled' => false, // Activer si vous avez une clé
            'key' => 'VOTRE_CLE_TOGETHER_ICI',
            'endpoint' => 'https://api.together.xyz/v1/chat/completions',
            'models' => [
                'mistralai/Mixtral-8x7B-Instruct-v0.1'
            ],
            'priority' => 3
        ]
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function handleRequest() {
        $data = json_decode(file_get_contents('php://input'), true);

        $question = $data['question'] ?? '';
        $context = $data['context'] ?? [];
        $language = $data['language'] ?? 'fr';
        $conversation_history = $data['conversation_history'] ?? [];

        if (empty($question)) {
            http_response_code(400);
            echo json_encode(['error' => 'Question vide']);
            return;
        }

        try {
            // Appeler l'IA avec cascade de modèles
            $result = $this->callAICascade($question, $context, $language, $conversation_history);

            echo json_encode([
                'success' => true,
                'answer' => $result['answer'],
                'model_used' => $result['model'],
                'confidence' => $result['confidence'],
                'tokens_used' => $result['tokens'],
                'response_time' => $result['time']
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'fallback' => $this->getFallbackAnswer($question, $language)
            ]);
        }
    }

    private function callAICascade($question, $context, $language, $history) {
        $startTime = microtime(true);

        // Trier les APIs par priorité
        $sortedAPIs = $this->apis;
        uasort($sortedAPIs, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        $errors = [];

        // Essayer chaque API dans l'ordre de priorité
        foreach ($sortedAPIs as $apiName => $apiConfig) {
            if (!$apiConfig['enabled']) continue;

            foreach ($apiConfig['models'] as $model) {
                try {
                    $result = $this->callSpecificAPI($apiName, $model, $apiConfig, $question, $context, $language, $history);

                    if ($result) {
                        $endTime = microtime(true);
                        return [
                            'answer' => $result,
                            'model' => "$apiName/$model",
                            'confidence' => 0.95,
                            'tokens' => strlen($result) / 4,
                            'time' => round($endTime - $startTime, 3)
                        ];
                    }
                } catch (Exception $e) {
                    $errors[] = "$apiName/$model: " . $e->getMessage();
                    continue;
                }
            }
        }

        throw new Exception('Tous les modèles ont échoué: ' . implode(', ', $errors));
    }

    private function callSpecificAPI($apiName, $model, $config, $question, $context, $language, $history) {
        switch ($apiName) {
            case 'groq':
                return $this->callGroqAPI($model, $config, $question, $context, $language, $history);

            case 'huggingface':
                return $this->callHuggingFaceAPI($model, $config, $question, $context, $language, $history);

            case 'together':
                return $this->callTogetherAPI($model, $config, $question, $context, $language, $history);

            default:
                throw new Exception("API inconnue: $apiName");
        }
    }

    private function callGroqAPI($model, $config, $question, $context, $language, $history) {
        $systemPrompt = $this->buildAdvancedSystemPrompt($language, $context);
        $messages = $this->buildMessages($systemPrompt, $question, $history);

        $data = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
            'top_p' => 0.9,
            'stream' => false
        ];

        $ch = curl_init($config['endpoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $config['key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Groq API error: HTTP $httpCode");
        }

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        }

        throw new Exception("Invalid Groq response format");
    }

    private function callHuggingFaceAPI($model, $config, $question, $context, $language, $history) {
        $prompt = $this->buildSmartPrompt($question, $context, $language, $history);

        $data = [
            'inputs' => $prompt,
            'parameters' => [
                'max_new_tokens' => 500,
                'temperature' => 0.7,
                'top_p' => 0.9,
                'do_sample' => true,
                'return_full_text' => false
            ]
        ];

        $ch = curl_init($config['endpoint'] . $model);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $config['key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Hugging Face error: HTTP $httpCode");
        }

        $result = json_decode($response, true);

        // Gérer différents formats de réponse
        if (isset($result[0]['generated_text'])) {
            return $result[0]['generated_text'];
        } elseif (isset($result['generated_text'])) {
            return $result['generated_text'];
        } elseif (is_array($result) && isset($result[0])) {
            return $result[0];
        }

        throw new Exception("Invalid Hugging Face response");
    }

    private function callTogetherAPI($model, $config, $question, $context, $language, $history) {
        // Similaire à Groq (format OpenAI)
        $systemPrompt = $this->buildAdvancedSystemPrompt($language, $context);
        $messages = $this->buildMessages($systemPrompt, $question, $history);

        $data = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 1024,
            'temperature' => 0.7
        ];

        $ch = curl_init($config['endpoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $config['key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Together API error: HTTP $httpCode");
        }

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        }

        throw new Exception("Invalid Together response");
    }

    private function buildAdvancedSystemPrompt($language, $context) {
        $prompts = [
            'fr' => "Tu es un assistant IA extrêmement intelligent et polyvalent, similaire à ChatGPT ou Claude.
Tu peux répondre à TOUTES les questions sur TOUS les sujets : sciences, mathématiques, histoire, géographie, technologie, philosophie, arts, littérature, etc.

Caractéristiques de tes réponses :
- Intelligentes et approfondies
- Claires et bien structurées
- Adaptées au niveau de l'utilisateur (enfants 8-12 ans)
- Avec des exemples concrets
- Engageantes et encourageantes
- Utilise des émojis pertinents

Contexte actuel : " . ($context['topic'] ?? 'Général') . "

Si tu ne connais pas une réponse, dis-le honnêtement et propose des pistes pour trouver l'information.",

            'ar' => "أنت مساعد ذكاء اصطناعي ذكي للغاية ومتعدد الاستخدامات، يشبه ChatGPT أو Claude.
يمكنك الإجابة على جميع الأسئلة حول جميع المواضيع: العلوم والرياضيات والتاريخ والجغرافيا والتكنولوجيا والفلسفة والفنون والأدب وغيرها.

خصائص إجاباتك:
- ذكية ومتعمقة
- واضحة ومنظمة بشكل جيد
- مناسبة لمستوى المستخدم (الأطفال 8-12 سنة)
- مع أمثلة ملموسة
- جذابة ومشجعة
- استخدم الرموز التعبيرية ذات الصلة

السياق الحالي: " . ($context['topic'] ?? 'عام') . "

إذا كنت لا تعرف الإجابة، قلها بصدق واقترح طرقاً للعثور على المعلومات."
        ];

        return $prompts[$language] ?? $prompts['fr'];
    }

    private function buildSmartPrompt($question, $context, $language, $history) {
        $systemPrompt = $this->buildAdvancedSystemPrompt($language, $context);

        $prompt = $systemPrompt . "\n\n";

        // Ajouter l'historique récent
        if (!empty($history)) {
            $prompt .= "Historique de la conversation:\n";
            foreach (array_slice($history, -3) as $item) {
                $prompt .= "User: " . $item['question'] . "\n";
                $prompt .= "Assistant: " . $item['answer'] . "\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "Question actuelle: $question\n\n";
        $prompt .= "Réponse détaillée et intelligente:";

        return $prompt;
    }

    private function buildMessages($systemPrompt, $question, $history) {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Ajouter l'historique
        foreach (array_slice($history, -5) as $item) {
            $messages[] = ['role' => 'user', 'content' => $item['question']];
            $messages[] = ['role' => 'assistant', 'content' => $item['answer']];
        }

        // Ajouter la question actuelle
        $messages[] = ['role' => 'user', 'content' => $question];

        return $messages;
    }

    private function getFallbackAnswer($question, $language) {
        $fallbacks = [
            'fr' => "Je suis désolé, je rencontre des difficultés techniques pour le moment. Peux-tu reformuler ta question ou réessayer dans quelques instants ?",
            'ar' => "أنا آسف، أواجه صعوبات تقنية في الوقت الحالي. هل يمكنك إعادة صياغة سؤالك أو المحاولة مرة أخرى بعد قليل؟"
        ];

        return $fallbacks[$language] ?? $fallbacks['fr'];
    }
}

// Exécution
try {
    $api = new AdvancedChatbotAPI();
    $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
