<?php
/**
 * ============================================================
 * NutriMind — Groq AI Backend Proxy
 * ============================================================
 * Receives a prompt from the frontend via POST,
 * sends it to Groq (Llama 3) and returns the AI response.
 *
 * FREE — no credit card required.
 * Get your key at: https://console.groq.com/keys
 *
 * SECURITY: API key never reaches the browser.
 * ============================================================
 */

header('Content-Type: application/json');

define('GROQ_API_KEY', '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');
define('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions');
define('GROQ_MODEL',    'llama-3.3-70b-versatile');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (empty($data['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucune donnée reçue.']);
    exit;
}

$payload = json_encode([
    'model'       => GROQ_MODEL,
    'messages'    => [
        [
            'role'    => 'system',
            'content' => 'Tu es un expert en nutrition et santé. Réponds toujours en français, de manière claire, structurée et bienveillante. Utilise des emojis pour rendre la réponse lisible. Sois précis et factuel.'
        ],
        [
            'role'    => 'user',
            'content' => $data['prompt']
        ]
    ],
    'max_tokens'  => 900,
    'temperature' => 0.6,
]);

$ch = curl_init(GROQ_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . GROQ_API_KEY,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 30,
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur réseau : ' . $curlError]);
    exit;
}

$result = json_decode($response, true);

if ($httpCode !== 200) {
    http_response_code(500);
    $msg = $result['error']['message'] ?? ('Erreur HTTP ' . $httpCode);
    echo json_encode(['error' => 'Groq : ' . $msg]);
    exit;
}

$text = $result['choices'][0]['message']['content'] ?? 'Aucune réponse reçue.';
echo json_encode(['response' => $text]);
