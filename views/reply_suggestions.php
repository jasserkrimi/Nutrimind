<?php
/**
 * AI Smart Reply Suggestions
 * Receives the original comment text, returns 3 short reply suggestions.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../config/ai_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié.']);
    exit;
}

$input   = json_decode(file_get_contents('php://input'), true);
$comment = isset($input['comment']) ? trim($input['comment']) : '';

if (strlen($comment) < 2) {
    echo json_encode(['success' => false, 'error' => 'Commentaire trop court.']);
    exit;
}

$systemPrompt = <<<PROMPT
You are a helpful assistant for a health and nutrition community platform.
A user wants to reply to a comment. Generate exactly 3 short, natural reply suggestions in the same language as the comment.
Each suggestion should be different in tone: one agreeing/supportive, one asking a follow-up question, one adding useful information or a different perspective.
Keep each suggestion under 100 characters. Be friendly and relevant to health/nutrition topics.
Respond ONLY with a valid JSON object in this exact format:
{"suggestions": ["reply one", "reply two", "reply three"]}
PROMPT;

$payload = [
    'model'       => GROQ_MODEL,
    'messages'    => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user',   'content' => 'Comment to reply to: ' . $comment],
    ],
    'temperature' => 0.7,
    'max_tokens'  => 200,
];

$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT    => 10,
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || $httpCode !== 200) {
    $msg = 'Service IA indisponible.';
    if ($httpCode === 429) $msg = 'Limite de requêtes IA atteinte. Réessayez dans un moment.';
    if ($httpCode === 401) $msg = 'Clé API invalide.';
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

$body    = json_decode($response, true);
$content = $body['choices'][0]['message']['content'] ?? '{}';

// Extract JSON from content (Groq may wrap it in markdown code blocks)
if (preg_match('/\{.*\}/s', $content, $matches)) {
    $content = $matches[0];
}
$result = json_decode($content, true);

if (empty($result['suggestions']) || !is_array($result['suggestions'])) {
    echo json_encode(['success' => false, 'error' => 'Réponse IA invalide.']);
    exit;
}

echo json_encode([
    'success'     => true,
    'suggestions' => array_map('htmlspecialchars', array_slice($result['suggestions'], 0, 3)),
]);
