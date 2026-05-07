<?php
/**
 * AI Comment Improver
 * Receives a raw comment draft, returns a clearer and more polite version.
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

$input = json_decode(file_get_contents('php://input'), true);
$draft = isset($input['draft']) ? trim($input['draft']) : '';

if (strlen($draft) < 2) {
    echo json_encode(['success' => false, 'error' => 'Commentaire trop court.']);
    exit;
}

$systemPrompt = <<<PROMPT
You are a writing assistant for a health and nutrition community platform.
The user has written a comment draft. Rewrite it to be:
- Clearer and better structured
- More polite and constructive
- Kept in the SAME language as the original
- Kept roughly the same length (do not make it much longer)
- Do NOT change the meaning or add new information

Respond ONLY with a valid JSON object in this exact format:
{"improved": "the improved comment text here"}
PROMPT;

$payload = [
    'model'       => GROQ_MODEL,
    'messages'    => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user',   'content' => 'Original comment: ' . $draft],
    ],
    'temperature' => 0.4,
    'max_tokens'  => 300,
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

// Extract JSON (Groq may wrap in markdown code blocks)
if (preg_match('/\{.*\}/s', $content, $matches)) {
    $content = $matches[0];
}
$result = json_decode($content, true);

if (empty($result['improved'])) {
    echo json_encode(['success' => false, 'error' => 'Réponse IA invalide.']);
    exit;
}

echo json_encode([
    'success'  => true,
    'improved' => htmlspecialchars($result['improved']),
]);
