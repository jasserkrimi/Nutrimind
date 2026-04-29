<?php
/**
 * Converts Markdown-like text from Groq into styled HTML cards.
 * Handles: ## headings, ### subheadings, **bold**, *italic*,
 *          - bullet lists, numbered lists, blank lines as paragraphs.
 */
function renderAIPlan(string $text): string {
    if (empty(trim($text))) return '<p class="text-muted">Aucun contenu généré.</p>';

    $lines  = explode("\n", $text);
    $html   = '';
    $inList = false;
    $inOl   = false;

    $closeList = function() use (&$html, &$inList, &$inOl) {
        if ($inList) { $html .= '</ul>'; $inList = false; }
        if ($inOl)   { $html .= '</ol>'; $inOl   = false; }
    };

    foreach ($lines as $line) {
        $line = rtrim($line);

        // H2
        if (preg_match('/^##\s+(.+)/', $line, $m)) {
            $closeList();
            $html .= '<h2 class="ai-h2">' . inline($m[1]) . '</h2>';
            continue;
        }
        // H3
        if (preg_match('/^###\s+(.+)/', $line, $m)) {
            $closeList();
            $html .= '<h3 class="ai-h3">' . inline($m[1]) . '</h3>';
            continue;
        }
        // H4
        if (preg_match('/^####\s+(.+)/', $line, $m)) {
            $closeList();
            $html .= '<h4 class="ai-h4">' . inline($m[1]) . '</h4>';
            continue;
        }
        // Numbered list
        if (preg_match('/^\d+\.\s+(.+)/', $line, $m)) {
            if ($inList) { $html .= '</ul>'; $inList = false; }
            if (!$inOl)  { $html .= '<ol class="ai-ol">'; $inOl = true; }
            $html .= '<li>' . inline($m[1]) . '</li>';
            continue;
        }
        // Bullet list (-, *, •)
        if (preg_match('/^[-*•]\s+(.+)/', $line, $m)) {
            if ($inOl)   { $html .= '</ol>'; $inOl = false; }
            if (!$inList){ $html .= '<ul class="ai-ul">'; $inList = true; }
            $html .= '<li>' . inline($m[1]) . '</li>';
            continue;
        }
        // Horizontal rule
        if (preg_match('/^---+$/', $line)) {
            $closeList();
            $html .= '<hr class="ai-hr">';
            continue;
        }
        // Empty line
        if (trim($line) === '') {
            $closeList();
            continue;
        }
        // Normal paragraph line
        $closeList();
        $html .= '<p class="ai-p">' . inline($line) . '</p>';
    }

    // Close any open list
    if ($inList) $html .= '</ul>';
    if ($inOl)   $html .= '</ol>';

    return $html;
}

function inline(string $text): string {
    // Escape HTML first
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    // Bold+italic ***text***
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
    // Bold **text**
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    // Italic *text*
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    // Inline code `text`
    $text = preg_replace('/`(.+?)`/', '<code class="ai-code">$1</code>', $text);
    return $text;
}
?>
