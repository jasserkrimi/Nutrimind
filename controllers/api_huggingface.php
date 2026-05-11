<?php
/**
 * ============================================================
 * NutriMind — Hugging Face Backend Proxy
 * ============================================================
 * Model   : nateraw/food  (101-class food classifier)
 * Endpoint: POST https://router.huggingface.co/hf-inference/models/nateraw/food
 * Auth    : Authorization: Bearer <HF_TOKEN>
 *
 * The model returns labels like "sushi", "pizza", "pad_thai" etc.
 * We clean the label and map it to an Edamam-friendly food name.
 * SECURITY: Token never reaches the browser.
 * ============================================================
 */

header('Content-Type: application/json');

define('HF_TOKEN',    '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');
define('HF_ENDPOINT', 'https://router.huggingface.co/hf-inference/models/nateraw/food');

// ── Map nateraw/food labels → Edamam-friendly search terms ───────────────────
// nateraw/food is trained on Food-101 dataset. These are all 101 classes.
// Values are what we send to Edamam for best nutrition results.
const FOOD_MAP = [
    'apple_pie'              => 'apple pie',
    'baby_back_ribs'         => 'pork ribs',
    'baklava'                => 'baklava',
    'beef_carpaccio'         => 'beef carpaccio',
    'beef_tartare'           => 'beef tartare',
    'beet_salad'             => 'beet salad',
    'beignets'               => 'beignets',
    'bibimbap'               => 'bibimbap',
    'bread_pudding'          => 'bread pudding',
    'breakfast_burrito'      => 'breakfast burrito',
    'bruschetta'             => 'bruschetta',
    'caesar_salad'           => 'caesar salad',
    'cannoli'                => 'cannoli',
    'caprese_salad'          => 'caprese salad',
    'carrot_cake'            => 'carrot cake',
    'ceviche'                => 'ceviche',
    'cheese_plate'           => 'cheese',
    'cheesecake'             => 'cheesecake',
    'chicken_curry'          => 'chicken curry',
    'chicken_quesadilla'     => 'chicken quesadilla',
    'chicken_wings'          => 'chicken wings',
    'chocolate_cake'         => 'chocolate cake',
    'chocolate_mousse'       => 'chocolate mousse',
    'churros'                => 'churros',
    'clam_chowder'           => 'clam chowder',
    'club_sandwich'          => 'club sandwich',
    'crab_cakes'             => 'crab cakes',
    'creme_brulee'           => 'creme brulee',
    'croque_madame'          => 'croque madame',
    'cup_cakes'              => 'cupcake',
    'deviled_eggs'           => 'deviled eggs',
    'donuts'                 => 'donut',
    'dumplings'              => 'dumplings',
    'edamame'                => 'edamame',
    'eggs_benedict'          => 'eggs benedict',
    'escargots'              => 'escargot',
    'falafel'                => 'falafel',
    'filet_mignon'           => 'filet mignon',
    'fish_and_chips'         => 'fish and chips',
    'foie_gras'              => 'foie gras',
    'french_fries'           => 'french fries',
    'french_onion_soup'      => 'french onion soup',
    'french_toast'           => 'french toast',
    'fried_calamari'         => 'fried calamari',
    'fried_rice'             => 'fried rice',
    'frozen_yogurt'          => 'frozen yogurt',
    'garlic_bread'           => 'garlic bread',
    'gnocchi'                => 'gnocchi',
    'greek_salad'            => 'greek salad',
    'grilled_cheese_sandwich'=> 'grilled cheese sandwich',
    'grilled_salmon'         => 'grilled salmon',
    'guacamole'              => 'guacamole',
    'gyoza'                  => 'gyoza',
    'hamburger'              => 'hamburger',
    'hot_and_sour_soup'      => 'hot and sour soup',
    'hot_dog'                => 'hot dog',
    'huevos_rancheros'       => 'huevos rancheros',
    'hummus'                 => 'hummus',
    'ice_cream'              => 'ice cream',
    'lasagna'                => 'lasagna',
    'lobster_bisque'         => 'lobster bisque',
    'lobster_roll_sandwich'  => 'lobster roll',
    'macaroni_and_cheese'    => 'macaroni and cheese',
    'macarons'               => 'macaron',
    'miso_soup'              => 'miso soup',
    'mussels'                => 'mussels',
    'nachos'                 => 'nachos',
    'omelette'               => 'omelette',
    'onion_rings'            => 'onion rings',
    'oysters'                => 'oysters',
    'pad_thai'               => 'pad thai',
    'paella'                 => 'paella',
    'pancakes'               => 'pancakes',
    'panna_cotta'            => 'panna cotta',
    'peking_duck'            => 'peking duck',
    'pho'                    => 'pho',
    'pizza'                  => 'pizza',
    'pork_chop'              => 'pork chop',
    'poutine'                => 'poutine',
    'prime_rib'              => 'prime rib',
    'pulled_pork_sandwich'   => 'pulled pork sandwich',
    'ramen'                  => 'ramen',
    'ravioli'                => 'ravioli',
    'red_velvet_cake'        => 'red velvet cake',
    'risotto'                => 'risotto',
    'samosa'                 => 'samosa',
    'sashimi'                => 'sashimi',
    'scallops'               => 'scallops',
    'seaweed_salad'          => 'seaweed salad',
    'shrimp_and_grits'       => 'shrimp and grits',
    'spaghetti_bolognese'    => 'spaghetti bolognese',
    'spaghetti_carbonara'    => 'spaghetti carbonara',
    'spring_rolls'           => 'spring rolls',
    'steak'                  => 'beef steak',
    'strawberry_shortcake'   => 'strawberry shortcake',
    'sushi'                  => 'sushi',
    'tacos'                  => 'tacos',
    'takoyaki'               => 'takoyaki',
    'tiramisu'               => 'tiramisu',
    'tuna_tartare'           => 'tuna tartare',
    'waffles'                => 'waffles',
];

// Accept POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (empty($data['image_base64'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucune image reçue.']);
    exit;
}

$base64 = $data['image_base64'];
if (strpos($base64, ',') !== false) {
    $base64 = explode(',', $base64)[1];
}

$imageBinary = base64_decode($base64);
if ($imageBinary === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Données image invalides.']);
    exit;
}

// ── Call Hugging Face ─────────────────────────────────────────────────────────
$ch = curl_init(HF_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $imageBinary,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . HF_TOKEN,
        'Content-Type: application/octet-stream',
    ],
    CURLOPT_TIMEOUT => 60,
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

if ($httpCode === 503) {
    http_response_code(503);
    echo json_encode(['error' => 'Le modèle IA est en cours de chargement. Réessayez dans quelques secondes.']);
    exit;
}

$result = json_decode($response, true);

if ($httpCode !== 200) {
    http_response_code(500);
    $msg = $result['error'] ?? ('Erreur HTTP ' . $httpCode);
    echo json_encode(['error' => 'Hugging Face : ' . $msg]);
    exit;
}

if (!is_array($result) || empty($result) || !isset($result[0]['label'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Aucun aliment détecté dans cette image.']);
    exit;
}

// ── Pick best label and map to Edamam-friendly name ──────────────────────────
// Use the top prediction. Map it through FOOD_MAP for a clean search term.
$topLabel = strtolower(trim($result[0]['label']));
$topScore = round($result[0]['score'] * 100, 1);

// Normalise: replace underscores/hyphens with spaces
$labelNorm = str_replace(['_', '-'], ' ', $topLabel);

// Look up in map (try original key first, then normalised)
$foodName = FOOD_MAP[$topLabel]
         ?? FOOD_MAP[str_replace(' ', '_', $topLabel)]
         ?? $labelNorm;

echo json_encode([
    'food'       => $foodName,
    'label'      => $labelNorm,   // original detected label (for display)
    'confidence' => $topScore,
]);
