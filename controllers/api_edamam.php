<?php
/**
 * ============================================================
 * NutriMind — Edamam Nutrition Proxy  (no DB interaction)
 * ============================================================
 * 1. Tries Edamam API  (reads ingredients[0].parsed[0].nutrients)
 * 2. If Edamam returns zero → uses built-in nutrition table
 * ============================================================
 */

header('Content-Type: application/json');

define('EDAMAM_APP_ID',   'f2e34427');
define('EDAMAM_APP_KEY',  '4c711cb91c25f78981f79a3699ff57ff');
define('EDAMAM_ENDPOINT', 'https://api.edamam.com/api/nutrition-data');

// ── Built-in fallback table (per 100 g) ──────────────────────────────────────
// Used when Edamam cannot parse the food name.
// Values are approximate averages from standard nutrition databases.
const NUTRITION_TABLE = [
    // Japanese
    'sushi'                  => ['cal'=>143, 'prot'=>5.2,  'fat'=>0.7,  'carb'=>30.0],
    'sashimi'                => ['cal'=>130, 'prot'=>20.0, 'fat'=>4.5,  'carb'=>0.0 ],
    'ramen'                  => ['cal'=>436, 'prot'=>18.0, 'fat'=>14.0, 'carb'=>54.0],
    'miso soup'              => ['cal'=>40,  'prot'=>3.0,  'fat'=>1.5,  'carb'=>4.0 ],
    'gyoza'                  => ['cal'=>218, 'prot'=>9.0,  'fat'=>9.0,  'carb'=>24.0],
    'takoyaki'               => ['cal'=>200, 'prot'=>8.0,  'fat'=>8.0,  'carb'=>22.0],
    'seaweed salad'          => ['cal'=>70,  'prot'=>1.5,  'fat'=>3.5,  'carb'=>8.0 ],
    // Italian
    'pizza'                  => ['cal'=>266, 'prot'=>11.0, 'fat'=>10.0, 'carb'=>33.0],
    'lasagna'                => ['cal'=>135, 'prot'=>8.0,  'fat'=>5.0,  'carb'=>14.0],
    'risotto'                => ['cal'=>166, 'prot'=>4.0,  'fat'=>5.0,  'carb'=>26.0],
    'gnocchi'                => ['cal'=>130, 'prot'=>3.0,  'fat'=>1.0,  'carb'=>28.0],
    'ravioli'                => ['cal'=>220, 'prot'=>9.0,  'fat'=>7.0,  'carb'=>30.0],
    'spaghetti bolognese'    => ['cal'=>163, 'prot'=>9.0,  'fat'=>5.0,  'carb'=>20.0],
    'spaghetti carbonara'    => ['cal'=>370, 'prot'=>14.0, 'fat'=>18.0, 'carb'=>38.0],
    'tiramisu'               => ['cal'=>240, 'prot'=>5.0,  'fat'=>13.0, 'carb'=>26.0],
    'panna cotta'            => ['cal'=>230, 'prot'=>3.0,  'fat'=>16.0, 'carb'=>19.0],
    'cannoli'                => ['cal'=>310, 'prot'=>6.0,  'fat'=>17.0, 'carb'=>34.0],
    'bruschetta'             => ['cal'=>180, 'prot'=>5.0,  'fat'=>6.0,  'carb'=>26.0],
    // American / Fast food
    'hamburger'              => ['cal'=>295, 'prot'=>17.0, 'fat'=>14.0, 'carb'=>24.0],
    'hot dog'                => ['cal'=>290, 'prot'=>11.0, 'fat'=>17.0, 'carb'=>23.0],
    'french fries'           => ['cal'=>312, 'prot'=>3.4,  'fat'=>15.0, 'carb'=>41.0],
    'onion rings'            => ['cal'=>411, 'prot'=>5.0,  'fat'=>21.0, 'carb'=>50.0],
    'nachos'                 => ['cal'=>346, 'prot'=>8.0,  'fat'=>19.0, 'carb'=>36.0],
    'club sandwich'          => ['cal'=>290, 'prot'=>18.0, 'fat'=>12.0, 'carb'=>27.0],
    'grilled cheese sandwich'=> ['cal'=>350, 'prot'=>14.0, 'fat'=>18.0, 'carb'=>32.0],
    'macaroni and cheese'    => ['cal'=>164, 'prot'=>6.0,  'fat'=>6.0,  'carb'=>20.0],
    'pulled pork sandwich'   => ['cal'=>280, 'prot'=>20.0, 'fat'=>10.0, 'carb'=>26.0],
    'chicken wings'          => ['cal'=>290, 'prot'=>27.0, 'fat'=>19.0, 'carb'=>0.0 ],
    'baby back ribs'         => ['cal'=>297, 'prot'=>26.0, 'fat'=>20.0, 'carb'=>0.0 ],
    'pork chop'              => ['cal'=>231, 'prot'=>25.0, 'fat'=>14.0, 'carb'=>0.0 ],
    'prime rib'              => ['cal'=>340, 'prot'=>26.0, 'fat'=>26.0, 'carb'=>0.0 ],
    'filet mignon'           => ['cal'=>271, 'prot'=>26.0, 'fat'=>18.0, 'carb'=>0.0 ],
    'beef steak'             => ['cal'=>271, 'prot'=>26.0, 'fat'=>18.0, 'carb'=>0.0 ],
    'steak'                  => ['cal'=>271, 'prot'=>26.0, 'fat'=>18.0, 'carb'=>0.0 ],
    // Breakfast
    'pancakes'               => ['cal'=>227, 'prot'=>6.0,  'fat'=>7.0,  'carb'=>35.0],
    'waffles'                => ['cal'=>291, 'prot'=>8.0,  'fat'=>11.0, 'carb'=>40.0],
    'french toast'           => ['cal'=>229, 'prot'=>8.0,  'fat'=>9.0,  'carb'=>28.0],
    'omelette'               => ['cal'=>154, 'prot'=>11.0, 'fat'=>11.0, 'carb'=>1.0 ],
    'eggs benedict'          => ['cal'=>250, 'prot'=>13.0, 'fat'=>16.0, 'carb'=>14.0],
    'breakfast burrito'      => ['cal'=>220, 'prot'=>11.0, 'fat'=>9.0,  'carb'=>24.0],
    'huevos rancheros'       => ['cal'=>180, 'prot'=>9.0,  'fat'=>9.0,  'carb'=>16.0],
    // Asian
    'pad thai'               => ['cal'=>230, 'prot'=>12.0, 'fat'=>8.0,  'carb'=>28.0],
    'fried rice'             => ['cal'=>163, 'prot'=>4.0,  'fat'=>4.0,  'carb'=>27.0],
    'bibimbap'               => ['cal'=>130, 'prot'=>7.0,  'fat'=>3.0,  'carb'=>19.0],
    'spring rolls'           => ['cal'=>165, 'prot'=>4.0,  'fat'=>7.0,  'carb'=>21.0],
    'dumplings'              => ['cal'=>218, 'prot'=>9.0,  'fat'=>9.0,  'carb'=>24.0],
    'pho'                    => ['cal'=>215, 'prot'=>15.0, 'fat'=>5.0,  'carb'=>25.0],
    'peking duck'            => ['cal'=>337, 'prot'=>19.0, 'fat'=>28.0, 'carb'=>0.0 ],
    'hot and sour soup'      => ['cal'=>95,  'prot'=>6.0,  'fat'=>3.0,  'carb'=>11.0],
    'chicken curry'          => ['cal'=>150, 'prot'=>12.0, 'fat'=>7.0,  'carb'=>10.0],
    // Mediterranean / Middle Eastern
    'hummus'                 => ['cal'=>177, 'prot'=>8.0,  'fat'=>10.0, 'carb'=>14.0],
    'falafel'                => ['cal'=>333, 'prot'=>13.0, 'fat'=>18.0, 'carb'=>32.0],
    'greek salad'            => ['cal'=>100, 'prot'=>3.0,  'fat'=>7.0,  'carb'=>7.0 ],
    'caesar salad'           => ['cal'=>190, 'prot'=>7.0,  'fat'=>15.0, 'carb'=>8.0 ],
    'beet salad'             => ['cal'=>74,  'prot'=>2.0,  'fat'=>3.0,  'carb'=>10.0],
    'caprese salad'          => ['cal'=>150, 'prot'=>8.0,  'fat'=>10.0, 'carb'=>5.0 ],
    'paella'                 => ['cal'=>200, 'prot'=>14.0, 'fat'=>6.0,  'carb'=>22.0],
    'ceviche'                => ['cal'=>90,  'prot'=>15.0, 'fat'=>1.5,  'carb'=>5.0 ],
    'samosa'                 => ['cal'=>262, 'prot'=>5.0,  'fat'=>13.0, 'carb'=>32.0],
    'baklava'                => ['cal'=>428, 'prot'=>5.0,  'fat'=>23.0, 'carb'=>52.0],
    // Seafood
    'grilled salmon'         => ['cal'=>208, 'prot'=>20.0, 'fat'=>13.0, 'carb'=>0.0 ],
    'fish and chips'         => ['cal'=>290, 'prot'=>14.0, 'fat'=>14.0, 'carb'=>28.0],
    'crab cakes'             => ['cal'=>185, 'prot'=>14.0, 'fat'=>9.0,  'carb'=>11.0],
    'lobster bisque'         => ['cal'=>180, 'prot'=>9.0,  'fat'=>11.0, 'carb'=>12.0],
    'lobster roll'           => ['cal'=>290, 'prot'=>18.0, 'fat'=>12.0, 'carb'=>28.0],
    'clam chowder'           => ['cal'=>150, 'prot'=>7.0,  'fat'=>7.0,  'carb'=>15.0],
    'mussels'                => ['cal'=>172, 'prot'=>24.0, 'fat'=>5.0,  'carb'=>7.0 ],
    'oysters'                => ['cal'=>81,  'prot'=>9.0,  'fat'=>2.0,  'carb'=>5.0 ],
    'scallops'               => ['cal'=>111, 'prot'=>20.0, 'fat'=>1.0,  'carb'=>5.0 ],
    'fried calamari'         => ['cal'=>175, 'prot'=>12.0, 'fat'=>7.0,  'carb'=>16.0],
    'tuna tartare'           => ['cal'=>130, 'prot'=>20.0, 'fat'=>4.0,  'carb'=>3.0 ],
    'beef tartare'           => ['cal'=>160, 'prot'=>20.0, 'fat'=>8.0,  'carb'=>1.0 ],
    'beef carpaccio'         => ['cal'=>150, 'prot'=>18.0, 'fat'=>8.0,  'carb'=>1.0 ],
    'foie gras'              => ['cal'=>462, 'prot'=>11.0, 'fat'=>44.0, 'carb'=>5.0 ],
    'escargot'               => ['cal'=>90,  'prot'=>16.0, 'fat'=>1.4,  'carb'=>2.0 ],
    // Desserts
    'ice cream'              => ['cal'=>207, 'prot'=>3.5,  'fat'=>11.0, 'carb'=>24.0],
    'cheesecake'             => ['cal'=>321, 'prot'=>5.5,  'fat'=>23.0, 'carb'=>25.0],
    'chocolate cake'         => ['cal'=>371, 'prot'=>5.0,  'fat'=>16.0, 'carb'=>52.0],
    'carrot cake'            => ['cal'=>415, 'prot'=>4.0,  'fat'=>21.0, 'carb'=>55.0],
    'red velvet cake'        => ['cal'=>370, 'prot'=>4.0,  'fat'=>16.0, 'carb'=>53.0],
    'strawberry shortcake'   => ['cal'=>280, 'prot'=>4.0,  'fat'=>12.0, 'carb'=>39.0],
    'apple pie'              => ['cal'=>237, 'prot'=>2.0,  'fat'=>11.0, 'carb'=>34.0],
    'bread pudding'          => ['cal'=>153, 'prot'=>5.0,  'fat'=>5.0,  'carb'=>22.0],
    'creme brulee'           => ['cal'=>332, 'prot'=>5.0,  'fat'=>22.0, 'carb'=>29.0],
    'chocolate mousse'       => ['cal'=>290, 'prot'=>5.0,  'fat'=>20.0, 'carb'=>24.0],
    'macarons'               => ['cal'=>400, 'prot'=>5.0,  'fat'=>15.0, 'carb'=>60.0],
    'macaron'                => ['cal'=>400, 'prot'=>5.0,  'fat'=>15.0, 'carb'=>60.0],
    'donuts'                 => ['cal'=>452, 'prot'=>5.0,  'fat'=>25.0, 'carb'=>51.0],
    'donut'                  => ['cal'=>452, 'prot'=>5.0,  'fat'=>25.0, 'carb'=>51.0],
    'churros'                => ['cal'=>357, 'prot'=>5.0,  'fat'=>16.0, 'carb'=>48.0],
    'beignets'               => ['cal'=>320, 'prot'=>5.0,  'fat'=>14.0, 'carb'=>44.0],
    'frozen yogurt'          => ['cal'=>127, 'prot'=>3.0,  'fat'=>2.0,  'carb'=>26.0],
    'cup cakes'              => ['cal'=>305, 'prot'=>3.0,  'fat'=>11.0, 'carb'=>48.0],
    'cupcake'                => ['cal'=>305, 'prot'=>3.0,  'fat'=>11.0, 'carb'=>48.0],
    // Snacks / Other
    'edamame'                => ['cal'=>122, 'prot'=>11.0, 'fat'=>5.0,  'carb'=>10.0],
    'deviled eggs'           => ['cal'=>185, 'prot'=>8.0,  'fat'=>16.0, 'carb'=>1.0 ],
    'guacamole'              => ['cal'=>160, 'prot'=>2.0,  'fat'=>15.0, 'carb'=>9.0 ],
    'garlic bread'           => ['cal'=>350, 'prot'=>8.0,  'fat'=>16.0, 'carb'=>44.0],
    'poutine'                => ['cal'=>740, 'prot'=>23.0, 'fat'=>36.0, 'carb'=>82.0],
    'tacos'                  => ['cal'=>226, 'prot'=>11.0, 'fat'=>10.0, 'carb'=>23.0],
    'chicken quesadilla'     => ['cal'=>290, 'prot'=>18.0, 'fat'=>13.0, 'carb'=>24.0],
    'shrimp and grits'       => ['cal'=>220, 'prot'=>16.0, 'fat'=>8.0,  'carb'=>22.0],
    'croque madame'          => ['cal'=>380, 'prot'=>20.0, 'fat'=>22.0, 'carb'=>26.0],
    'french onion soup'      => ['cal'=>172, 'prot'=>7.0,  'fat'=>7.0,  'carb'=>20.0],
    'lobster roll sandwich'  => ['cal'=>290, 'prot'=>18.0, 'fat'=>12.0, 'carb'=>28.0],
];

// Accept POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (empty($data['food'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucun aliment spécifié.']);
    exit;
}

$foodName = strtolower(trim($data['food']));

// ── Helper: query Edamam ──────────────────────────────────────────────────────
function queryEdamam(string $ingr): ?array {
    $url = EDAMAM_ENDPOINT
         . '?app_id='  . urlencode(EDAMAM_APP_ID)
         . '&app_key=' . urlencode(EDAMAM_APP_KEY)
         . '&ingr='    . urlencode($ingr);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET        => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;
    return json_decode($response, true);
}

// ── Helper: extract nutrients from Edamam response ───────────────────────────
function extractFromEdamam(?array $result): array {
    if (!$result) return ['calories'=>0,'protein'=>0,'fat'=>0,'carbs'=>0];

    // Plan A: top-level totalNutrients (full Edamam plan)
    if (!empty($result['totalNutrients']['ENERC_KCAL']['quantity'])) {
        $n = $result['totalNutrients'];
        return [
            'calories' => (int) round($result['calories'] ?? 0),
            'protein'  => round($n['PROCNT']['quantity'] ?? 0, 1),
            'fat'      => round($n['FAT']['quantity']    ?? 0, 1),
            'carbs'    => round($n['CHOCDF']['quantity'] ?? 0, 1),
        ];
    }

    // Plan B: ingredients[0].parsed[0].nutrients (basic Edamam plan)
    $parsed = $result['ingredients'][0]['parsed'][0] ?? null;
    if ($parsed && ($parsed['status'] ?? '') === 'OK'
        && !empty($parsed['nutrients']['ENERC_KCAL']['quantity'])) {
        $n = $parsed['nutrients'];
        return [
            'calories' => (int) round($n['ENERC_KCAL']['quantity'] ?? 0),
            'protein'  => round($n['PROCNT']['quantity']           ?? 0, 1),
            'fat'      => round($n['FAT']['quantity']              ?? 0, 1),
            'carbs'    => round($n['CHOCDF']['quantity']           ?? 0, 1),
        ];
    }

    return ['calories'=>0,'protein'=>0,'fat'=>0,'carbs'=>0];
}

// ── Step 1: Try Edamam with quantity prefix ───────────────────────────────────
$firstWord = strtok($foodName, ' ') ?: $foodName;
$attempts  = array_unique([
    '100g ' . $foodName,
    '100g ' . $firstWord,
    '1 serving ' . $foodName,
]);

$nutrients = ['calories'=>0,'protein'=>0,'fat'=>0,'carbs'=>0];
foreach ($attempts as $ingr) {
    $r = queryEdamam($ingr);
    $n = extractFromEdamam($r);
    if ($n['calories'] > 0) {
        $nutrients = $n;
        break;
    }
}

// ── Step 2: Fallback to built-in table if Edamam returned zero ───────────────
if ($nutrients['calories'] === 0) {
    // Try exact match first, then partial match
    if (isset(NUTRITION_TABLE[$foodName])) {
        $t = NUTRITION_TABLE[$foodName];
    } else {
        // Find the first table entry whose key appears in the food name
        $t = null;
        foreach (NUTRITION_TABLE as $key => $val) {
            if (strpos($foodName, $key) !== false || strpos($key, $firstWord) !== false) {
                $t = $val;
                break;
            }
        }
    }

    if ($t) {
        $nutrients = [
            'calories' => $t['cal'],
            'protein'  => $t['prot'],
            'fat'      => $t['fat'],
            'carbs'    => $t['carb'],
        ];
    }
}

echo json_encode([
    'food'     => $foodName,
    'calories' => $nutrients['calories'],
    'protein'  => $nutrients['protein'],
    'fat'      => $nutrients['fat'],
    'carbs'    => $nutrients['carbs'],
]);
