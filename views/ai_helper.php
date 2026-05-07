<?php
/**
 * Show a full-page loading spinner, then auto-submit a hidden form via POST.
 * Call this during GET requests before the AI call, then exit.
 *
 * @param string $action  The form action URL (e.g. "ai_planning.php?objectif_id=3")
 * @param string $emoji   Big emoji shown in the box
 * @param string $title   Heading text
 * @param string $subtitle Subtext below the heading
 * @param string $spinnerColor  CSS color for the spinner top border
 * @param string $bgGradient   CSS gradient for the page background
 */
function showLoadingScreen(
    string $action,
    string $emoji        = '🤖',
    string $title        = 'Génération en cours…',
    string $subtitle     = "L'IA prépare votre contenu.",
    string $spinnerColor = '#6366f1',
    string $bgGradient   = 'linear-gradient(135deg,#667eea,#764ba2)',
    string $extraFields  = ''   // optional extra <input> tags inside the form
): void {
    echo '<!DOCTYPE html><html lang="fr"><head>
  <meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:' . $bgGradient . ';display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:\'Segoe UI\',sans-serif}
    .box{text-align:center;padding:3rem 2.5rem;background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.25);max-width:460px;width:90%}
    .icon{font-size:3.5rem;margin-bottom:1rem;animation:pulse 1.5s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}
    .spinner{width:56px;height:56px;border:5px solid #e5e7eb;border-top-color:' . $spinnerColor . ';border-radius:50%;animation:spin .9s linear infinite;margin:0 auto 1.5rem}
    @keyframes spin{to{transform:rotate(360deg)}}
    h2{color:#1f2937;font-size:1.4rem;margin-bottom:.6rem}
    p{color:#6b7280;font-size:.93rem;line-height:1.6}
  </style>
</head><body>
  <div class="box">
    <div class="icon">' . $emoji . '</div>
    <div class="spinner"></div>
    <h2>' . htmlspecialchars($title) . '</h2>
    <p>' . htmlspecialchars($subtitle) . '</p>
  </div>
  <form id="f" method="POST" action="' . htmlspecialchars($action) . '" style="display:none">' . $extraFields . '</form>
  <script>document.addEventListener(\'DOMContentLoaded\',()=>document.getElementById(\'f\').submit())</script>
</body></html>';
}

/**
 * Send a prompt to Ollama and return the response text.
 * Returns null on failure and sets $error message.
 */
function askOllama(string $prompt, bool $jsonFormat = false): ?string {
    $payload = json_encode([
        "model"   => OLLAMA_MODEL,
        "prompt"  => $prompt,
        "stream"  => false,
        "format"  => $jsonFormat ? "json" : "",
        "options" => ["temperature" => 0.6, "num_predict" => 1500]
    ]);

    $ch = curl_init(OLLAMA_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ["Content-Type: application/json", "Origin: http://localhost"],
        CURLOPT_TIMEOUT        => 180,
        CURLOPT_PROXY          => '',
        CURLOPT_NOPROXY        => '*',
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    if (!$response || $http_code !== 200) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['response'] ?? null;
}
