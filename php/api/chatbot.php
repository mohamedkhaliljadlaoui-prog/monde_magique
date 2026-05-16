<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/database.php';

final class ChatbotAPI {
    private const DEFAULT_DAILY_LIMIT = 50;
    private const HF_CHAT_COMPLETIONS_URL = 'https://router.huggingface.co/v1/chat/completions';
    private const HF_MODEL = 'mistralai/Mistral-7B-Instruct-v0.2';

    private ?PDO $db = null;

    public function __construct() {
        try {
            $this->db = getDB();
        } catch (Throwable $e) {
            // DB optionnelle: le chatbot doit fonctionner même sans DB.
            $this->db = null;
        }
    }

    public function handleRequest(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'POST') {
            $this->handleQuestion();
            return;
        }

        if ($method === 'GET') {
            $this->getChatHistory();
            return;
        }

        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    }

    private function handleQuestion(): void {
        $payload = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'JSON invalide']);
            return;
        }

        // Compat: accepter aussi {message:"..."}
        $question = trim((string)($payload['question'] ?? $payload['message'] ?? ''));
        $context = is_array($payload['context'] ?? null) ? $payload['context'] : [];
        $language = $this->normalizeLanguage((string)($payload['language'] ?? $context['language'] ?? ''));
        if ($language === '') {
            $language = $this->detectLanguageFromText($question);
        }
        $history = is_array($payload['conversation_history'] ?? null) ? $payload['conversation_history'] : [];
        $sessionId = trim((string)($payload['session_id'] ?? $context['session_id'] ?? ''));
        if ($sessionId === '') {
            $sessionId = 'chat_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(6)), 0, 12);
        }

        $userId = (int)($payload['user_id'] ?? $context['user_id'] ?? 1);
        if ($userId <= 0) {
            $userId = 1;
        }

        if ($question === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Question vide']);
            return;
        }

        $dailyLimit = $this->getDailyLimit();
        $usageCount = $this->getDailyUsage($userId);
        if ($usageCount >= $dailyLimit) {
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'error' => 'Limite quotidienne atteinte',
                'remaining_questions' => 0
            ]);
            return;
        }

        $startedAt = microtime(true);
        $ai = $this->callHFChatCompletions($question, $context, $language, $history);
        $elapsedMs = (int)round((microtime(true) - $startedAt) * 1000);

        $this->saveConversation($userId, $sessionId, $question, $ai['answer'], $context, $language, $ai['model'], $ai['tokens'] ?? null, $elapsedMs);
        $this->updateUserStats($userId);

        echo json_encode([
            'success' => true,
            'answer' => $ai['answer'],
            'model_used' => $ai['model'],
            'remaining_questions' => max(0, $dailyLimit - $usageCount - 1),
            'response_time_ms' => $elapsedMs
        ]);
    }

    private function getChatHistory(): void {
        // Historique optionnel si DB dispo
        $userId = (int)($_GET['user_id'] ?? 0);
        $limit = (int)($_GET['limit'] ?? 20);
        $offset = (int)($_GET['offset'] ?? 0);
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);

        if ($this->db === null || $userId <= 0) {
            echo json_encode(['success' => true, 'data' => []]);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT id, user_id, stage_id, station_id, session_id, user_message, bot_response, language, created_at
                FROM chatbot_conversations
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $history = [];
        }

        echo json_encode(['success' => true, 'data' => $history]);
    }

    private function callHFChatCompletions(string $question, array $context, string $language, array $history): array {
        $apiKey = getenv('HF_TOKEN') ?: getenv('HF_API_KEY') ?: 'YOUR_HF_TOKEN';

        if (!is_string($apiKey) || trim($apiKey) === '') {
            return ['answer' => $this->getFallbackAnswer($language), 'model' => 'fallback/offline'];
        }

        $systemPrompt = $this->buildSystemPrompt($language, $context);
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        foreach ($this->normalizeHistory($history) as $turn) {
            $messages[] = $turn;
        }
        $messages[] = ['role' => 'user', 'content' => $question];

        $requestBody = [
            'model' => self::HF_MODEL,
            'messages' => $messages,
            'max_tokens' => 350,
            'temperature' => 0.6,
            'stream' => false
        ];

        $ch = curl_init(self::HF_CHAT_COMPLETIONS_URL);
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

        if ($error || $httpCode !== 200) {
            return ['answer' => $this->getFallbackAnswer($language), 'model' => 'fallback/hf-error'];
        }

        $data = json_decode((string)$response, true);
        $content = null;
        if (is_array($data) && isset($data['choices'][0]['message']['content'])) {
            $content = trim((string)$data['choices'][0]['message']['content']);
        }

        if ($content === null || $content === '') {
            return ['answer' => $this->getFallbackAnswer($language), 'model' => 'fallback/hf-empty'];
        }

        $tokens = null;
        if (is_array($data) && isset($data['usage']['total_tokens'])) {
            $tokens = (int)$data['usage']['total_tokens'];
        }

        return [
            'answer' => $content,
            'model' => 'huggingface-router/' . self::HF_MODEL,
            'tokens' => $tokens
        ];
    }

    private function buildSystemPrompt(string $language, array $context): string {
        $stage = (string)($context['stage'] ?? $context['stage_id'] ?? '');
        $stageKey = (string)($context['stage_key'] ?? '');
        $station = (string)($context['station'] ?? $context['station_id'] ?? '');
        $topic = (string)($context['topic'] ?? $context['stage_title'] ?? $context['title'] ?? '');

        if ($language === 'ar') {
            return "أنت مساعد تعليمي ودود للأطفال (8-12 سنة) داخل لعبة Monde Magique.\n"
                . "مهمتك: شرح الدرس ببساطة، إعطاء مثال صغير، ثم سؤال قصير للتأكد من الفهم عند الحاجة.\n"
                . "قواعد مهمة: إجابة قصيرة وواضحة (3-6 جمل)، لا محتوى مخيف/غير مناسب للأطفال، وإذا السؤال غير واضح اسأل سؤال توضيح واحد.\n"
                . "السياق الحالي: مرحلة={$stage} مفتاح={$stageKey} محطة={$station} موضوع=" . ($topic !== '' ? $topic : 'عام') . "\n";
        }

        return "Tu es un assistant pédagogique et bienveillant pour enfants (8-12 ans) dans le jeu Monde Magique.\n"
            . "Objectif: expliquer simplement, donner un exemple concret, puis proposer 1 mini-question de vérification si utile.\n"
            . "Règles: réponse courte (3-6 phrases), pas de contenu effrayant/inadapté, et si la question est floue pose 1 question de clarification.\n"
            . "Contexte: stage={$stage} key={$stageKey} station={$station} topic=" . ($topic !== '' ? $topic : 'Général') . "\n";
    }

    private function normalizeHistory(array $history): array {
        $out = [];
        foreach (array_slice($history, -8) as $item) {
            if (!is_array($item)) {
                continue;
            }

            // Formats acceptés:
            // - {role:"user"|"assistant", content:"..."}
            // - {sender:"user"|"bot", text:"..."}
            // - {question:"...", answer:"..."}
            if (isset($item['role'], $item['content']) && is_string($item['role']) && is_string($item['content'])) {
                $role = $item['role'] === 'assistant' ? 'assistant' : ($item['role'] === 'user' ? 'user' : null);
                if ($role) {
                    $content = trim($item['content']);
                    if ($content !== '') {
                        $out[] = ['role' => $role, 'content' => $content];
                    }
                }
                continue;
            }

            if (isset($item['sender'], $item['text']) && is_string($item['sender']) && is_string($item['text'])) {
                $role = $item['sender'] === 'bot' ? 'assistant' : ($item['sender'] === 'user' ? 'user' : null);
                if ($role) {
                    $content = trim($item['text']);
                    if ($content !== '') {
                        $out[] = ['role' => $role, 'content' => $content];
                    }
                }
                continue;
            }

            if (isset($item['question'], $item['answer']) && is_string($item['question']) && is_string($item['answer'])) {
                $q = trim($item['question']);
                $a = trim($item['answer']);
                if ($q !== '') {
                    $out[] = ['role' => 'user', 'content' => $q];
                }
                if ($a !== '') {
                    $out[] = ['role' => 'assistant', 'content' => $a];
                }
            }
        }
        return $out;
    }

    private function saveConversation(
        int $userId,
        string $sessionId,
        string $question,
        string $answer,
        array $context,
        string $language,
        string $modelUsed,
        ?int $tokensUsed,
        int $responseTimeMs
    ): void {
        if ($this->db === null) {
            return;
        }

        $stageId = isset($context['stage']) ? (int)$context['stage'] : (isset($context['stage_id']) ? (int)$context['stage_id'] : null);
        $stationId = isset($context['station']) ? (int)$context['station'] : (isset($context['station_id']) ? (int)$context['station_id'] : null);
        $ctxJson = json_encode($context, JSON_UNESCAPED_UNICODE);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO chatbot_conversations
                    (user_id, stage_id, station_id, session_id, user_message, bot_response, ai_model, tokens_used, confidence_score, response_time_ms, context, language)
                VALUES
                    (:user_id, :stage_id, :station_id, :session_id, :user_message, :bot_response, :ai_model, :tokens_used, :confidence_score, :response_time_ms, :context, :language)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':stage_id' => $stageId ?: null,
                ':station_id' => $stationId ?: null,
                ':session_id' => $sessionId,
                ':user_message' => $question,
                ':bot_response' => $answer,
                ':ai_model' => $modelUsed,
                ':tokens_used' => $tokensUsed,
                ':confidence_score' => 0.9,
                ':response_time_ms' => $responseTimeMs,
                ':context' => $ctxJson,
                ':language' => $language
            ]);
        } catch (Throwable $e) {
            // Ignorer les erreurs DB (table absente, etc.)
        }
    }

    private function getDailyUsage(int $userId): int {
        if ($this->db === null) {
            return 0;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) AS cnt
                FROM chatbot_conversations
                WHERE user_id = ? AND DATE(created_at) = CURDATE()
            ");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['cnt'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    private function updateUserStats(int $userId): void {
        if ($this->db === null) {
            return;
        }

        try {
            // Créer si absent
            $stmt = $this->db->prepare("SELECT user_id FROM user_statistics WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $exists = (bool)$stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exists) {
                $stmt = $this->db->prepare("INSERT INTO user_statistics (user_id, chatbot_questions_asked) VALUES (?, 0)");
                $stmt->execute([$userId]);
            }

            $stmt = $this->db->prepare("UPDATE user_statistics SET chatbot_questions_asked = COALESCE(chatbot_questions_asked, 0) + 1 WHERE user_id = ?");
            $stmt->execute([$userId]);
        } catch (Throwable $e) {
            // Ignore
        }
    }

    private function getDailyLimit(): int {
        $configPath = __DIR__ . '/../../config.json';
        if (!file_exists($configPath)) {
            return self::DEFAULT_DAILY_LIMIT;
        }

        try {
            $raw = file_get_contents($configPath);
            $cfg = json_decode((string)$raw, true);
            $limit = (int)($cfg['game']['daily_chatbot_limit'] ?? self::DEFAULT_DAILY_LIMIT);
            return $limit > 0 ? $limit : self::DEFAULT_DAILY_LIMIT;
        } catch (Throwable $e) {
            return self::DEFAULT_DAILY_LIMIT;
        }
    }

    private function normalizeLanguage(string $language): string {
        $lang = strtolower(trim($language));
        if ($lang === 'ar' || $lang === 'fr') {
            return $lang;
        }
        return '';
    }

    private function detectLanguageFromText(string $text): string {
        // Détection simple: présence de caractères arabes
        return (preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1) ? 'ar' : 'fr';
    }

    private function getFallbackAnswer(string $language): string {
        if ($language === 'ar') {
            return 'عذرًا 😔 لم أستطع الإجابة الآن. جرّب صياغة السؤال بطريقة أبسط أو قل لي اسم الدرس/المحطة.';
        }
        return "Désolé 😔 je n'arrive pas à répondre pour l'instant. Reformule ta question ou dis-moi la station/lesson.";
    }
}

try {
    (new ChatbotAPI())->handleRequest();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur interne']);
}