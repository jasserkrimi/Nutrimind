<?php
/**
 * NutriMind — Nutritional Anomaly Detector Page
 * Scans last 30 days of meals and groups nutritional issues (read-only)
 */
session_start();
require_once '../config/Database.php';

$alerts        = [];
$detectorErr   = null;
$mealNutrition = [];

try {
    $db   = new Database();
    $conn = $db->connect();

    // Use LEFT JOIN so meals without ingredients are still included
    $stmt = $conn->prepare("
        SELECT
            m.id AS meal_id,
            m.name AS meal_name,
            m.date AS meal_date,
            ROUND(COALESCE(SUM(i.calories * mi.quantity / 100), 0), 1) AS total_calories,
            ROUND(COALESCE(SUM(i.proteins * mi.quantity / 100), 0), 1) AS total_proteins,
            ROUND(COALESCE(SUM(i.lipides  * mi.quantity / 100), 0), 1) AS total_fat,
            ROUND(COALESCE(SUM(i.glucides * mi.quantity / 100), 0), 1) AS total_carbs,
            COUNT(mi.ingredient_id) AS ingredient_count
        FROM Meal m
        LEFT JOIN Meal_Ingredient mi ON mi.meal_id = m.id
        LEFT JOIN Ingredient i ON i.id = mi.ingredient_id
        GROUP BY m.id, m.name, m.date
        ORDER BY m.date DESC
    ");
    $stmt->execute();
    $mealNutrition = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $THRESH = [
        'cal_high'  => 700,   // > 700 kcal = high
        'cal_low'   => 200,   // < 200 kcal = too light
        'fat_high'  => 25,    // > 25g fat = high
        'prot_low'  => 10,    // < 10g protein = low
        'carb_high' => 80,    // > 80g carbs = high
        'carb_low'  => 15,    // < 15g carbs = very low
    ];

    $issues = [
        'cal_high'  => ['level'=>'red',    'icon'=>'🔴', 'message'=>'Calories très élevées',   'advice'=>'Réduisez les portions ou remplacez les ingrédients caloriques.', 'meals'=>[]],
        'fat_high'  => ['level'=>'red',    'icon'=>'🔴', 'message'=>'Lipides critiques',        'advice'=>'Privilégiez des cuissons légères et des protéines maigres.',      'meals'=>[]],
        'carb_high' => ['level'=>'red',    'icon'=>'🔴', 'message'=>'Surcharge en glucides',    'advice'=>'Réduisez les féculents et sucres ajoutés.',                        'meals'=>[]],
        'prot_low'  => ['level'=>'yellow', 'icon'=>'🟡', 'message'=>'Protéines insuffisantes',  'advice'=>'Ajoutez des œufs, légumineuses ou viande maigre.',                 'meals'=>[]],
        'cal_low'   => ['level'=>'yellow', 'icon'=>'🟡', 'message'=>'Repas trop léger',         'advice'=>'Enrichissez ces repas avec des aliments nutritifs.',               'meals'=>[]],
        'carb_low'  => ['level'=>'yellow', 'icon'=>'🟡', 'message'=>'Glucides très faibles',    'advice'=>'Ajoutez des céréales complètes ou des fruits.',                    'meals'=>[]],
        'balanced'  => ['level'=>'green',  'icon'=>'🟢', 'message'=>'Repas bien équilibrés',    'advice'=>'Continuez sur cette lancée !',                                     'meals'=>[]],
    ];

    $totalMeals = count($mealNutrition);

    foreach ($mealNutrition as $m) {
        $cal   = (float) $m['total_calories'];
        $prot  = (float) $m['total_proteins'];
        $fat   = (float) $m['total_fat'];
        $carbs = (float) $m['total_carbs'];
        $label = htmlspecialchars($m['meal_name']) . ' (' . htmlspecialchars($m['meal_date']) . ')';

        if ($cal  > $THRESH['cal_high'])                $issues['cal_high']['meals'][]  = ['label'=>$label, 'val'=>"{$cal} kcal"];
        if ($fat  > $THRESH['fat_high'])                $issues['fat_high']['meals'][]  = ['label'=>$label, 'val'=>"{$fat}g"];
        if ($carbs > $THRESH['carb_high'])              $issues['carb_high']['meals'][] = ['label'=>$label, 'val'=>"{$carbs}g"];
        if ($prot < $THRESH['prot_low'] && $cal > 100)  $issues['prot_low']['meals'][]  = ['label'=>$label, 'val'=>"{$prot}g prot."];
        if ($cal  < $THRESH['cal_low']  && $cal > 0)    $issues['cal_low']['meals'][]   = ['label'=>$label, 'val'=>"{$cal} kcal"];
        if ($carbs < $THRESH['carb_low'] && $cal > 150) $issues['carb_low']['meals'][]  = ['label'=>$label, 'val'=>"{$carbs}g"];

        // Balanced: reasonable calories, decent protein, not too much fat or carbs
        if ($cal >= 200 && $cal <= 700 && $prot >= 10 && $fat <= 25 && $carbs >= 15 && $carbs <= 80)
            $issues['balanced']['meals'][] = ['label'=>$label, 'val'=>"{$cal} kcal"];
    }

    foreach (['cal_high','fat_high','carb_high','prot_low','cal_low','carb_low','balanced'] as $key) {
        $issue = $issues[$key];
        $count = count($issue['meals']);
        if ($count === 0) continue;
        $examples    = array_slice($issue['meals'], 0, 3);
        $exampleHtml = implode(', ', array_map(fn($e) => "<em>{$e['label']}</em> ({$e['val']})", $examples));
        if ($count > 3) $exampleHtml .= " <em>et " . ($count - 3) . " autre(s)…</em>";
        $alerts[] = [
            'level'   => $issue['level'],
            'icon'    => $issue['icon'],
            'message' => $issue['message'],
            'count'   => $count,
            'total'   => $totalMeals,
            'detail'  => "{$count} repas concerné(s) sur {$totalMeals} analysés : {$exampleHtml}. <strong>{$issue['advice']}</strong>",
        ];
    }

    usort($alerts, function($a, $b) {
        $order = ['red'=>0,'yellow'=>1,'green'=>2];
        return $order[$a['level']] <=> $order[$b['level']];
    });

} catch (Exception $e) {
    $detectorErr = "Impossible d'analyser les repas.";
}
?>
<?php include 'header.php'; ?>

<!-- ── Floating food particles ── -->
<div class="food-particles" id="foodParticles" aria-hidden="true"></div>
<script>
(function(){
    var e=['🥗','🍎','🥦','🍋','🥕','🍇','🥑','🍓','🌽','🥝','🍊','🫐'];
    var c=document.getElementById('foodParticles');
    for(var i=0;i<20;i++){
        var s=document.createElement('span');
        s.textContent=e[i%e.length];
        s.style.left=(Math.random()*100)+'%';
        s.style.fontSize=(16+Math.random()*20)+'px';
        s.style.animationDuration=(12+Math.random()*20)+'s';
        s.style.animationDelay=(Math.random()*16)+'s';
        c.appendChild(s);
    }
}());
</script>

<div class="product-section mt-150 mb-150">
<div class="container">

    <!-- Title -->
    <div class="row">
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3><span class="orange-text">Détecteur</span> d'Anomalies Nutritionnelles</h3>
                <p>Analyse automatique de tous vos repas — alertes groupées par type de problème.</p>
            </div>
        </div>
    </div>

    <!-- Back button -->
    <div class="row mb-4">
        <div class="col-lg-12 text-center">
            <a href="meal_list.php" class="boxed-btn"><i class="fas fa-arrow-left mr-1"></i> Retour aux repas</a>
        </div>
    </div>

    <!-- 30-day notice -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="alert alert-info mb-0" style="border-left: 4px solid #f28123; background:#fff8f2; color:#555; border-color:#f28123;">
                <i class="fas fa-calendar-alt mr-2" style="color:#f28123;"></i>
                <strong>Période analysée :</strong> les <strong>30 derniers jours</strong> uniquement.
                Les repas antérieurs à cette période ne sont pas inclus dans l'analyse.
            </div>
        </div>
    </div>

    <?php if ($detectorErr): ?>
        <div class="row justify-content-center"><div class="col-lg-10">
            <div class="alert alert-warning"><?php echo htmlspecialchars($detectorErr); ?></div>
        </div></div>

    <?php elseif (empty($mealNutrition)): ?>
        <div class="row justify-content-center"><div class="col-lg-10 text-center">
            <div class="detector-empty">
                <i class="fas fa-utensils"></i>
                <p>Aucun repas avec ingrédients trouvé dans les 30 derniers jours.</p>
            </div>
        </div></div>

    <?php else: ?>

    <!-- Summary counters -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="detector-summary">
                <?php
                    $countRed    = count(array_filter($alerts, fn($a) => $a['level']==='red'));
                    $countYellow = count(array_filter($alerts, fn($a) => $a['level']==='yellow'));
                    $countGreen  = count(array_filter($alerts, fn($a) => $a['level']==='green'));
                ?>
                <div class="detector-counter detector-counter-red">
                    <span class="detector-counter-num"><?php echo $countRed; ?></span>
                    <span class="detector-counter-lbl">Critique<?php echo $countRed>1?'s':''; ?></span>
                </div>
                <div class="detector-counter detector-counter-yellow">
                    <span class="detector-counter-num"><?php echo $countYellow; ?></span>
                    <span class="detector-counter-lbl">Avertissement<?php echo $countYellow>1?'s':''; ?></span>
                </div>
                <div class="detector-counter detector-counter-green">
                    <span class="detector-counter-num"><?php echo $countGreen; ?></span>
                    <span class="detector-counter-lbl">Équilibré<?php echo $countGreen>1?'s':''; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert cards -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php foreach ($alerts as $alert): ?>
            <div class="detector-alert detector-alert-<?php echo $alert['level']; ?>">
                <div class="detector-alert-header">
                    <span class="detector-alert-icon"><?php echo $alert['icon']; ?></span>
                    <div class="detector-alert-meta">
                        <strong class="detector-alert-meal"><?php echo htmlspecialchars($alert['message']); ?></strong>
                        <span class="detector-alert-date">
                            <?php echo $alert['count']; ?> repas sur <?php echo $alert['total']; ?> analysés — 30 derniers jours
                        </span>
                    </div>
                    <span class="detector-alert-badge detector-badge-<?php echo $alert['level']; ?>">
                        <?php echo $alert['count']; ?> / <?php echo $alert['total']; ?>
                    </span>
                </div>
                <div class="detector-alert-detail"><?php echo $alert['detail']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php endif; ?>

</div>
</div>

<?php include 'footer.php'; ?>

<style>
.detector-empty { padding: 40px 20px; color: #aaa; text-align: center; }
.detector-empty i { font-size: 48px; color: #28a745; display: block; margin-bottom: 14px; }
.detector-empty p { font-size: 15px; margin: 0; }
.detector-summary { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
.detector-counter { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 110px; height: 90px; border-radius: 12px; box-shadow: 0 3px 12px rgba(0,0,0,.08); }
.detector-counter-num { font-size: 32px; font-weight: 700; line-height: 1; }
.detector-counter-lbl { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-top: 4px; }
.detector-counter-red    { background: #fff5f5; color: #dc3545; }
.detector-counter-yellow { background: #fffbf0; color: #e6a817; }
.detector-counter-green  { background: #f0fff4; color: #28a745; }
.detector-alert { border-radius: 10px; margin-bottom: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; }
.detector-alert:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.12); }
.detector-alert-red    { border-left: 5px solid #dc3545; background: #fff; }
.detector-alert-yellow { border-left: 5px solid #ffc107; background: #fff; }
.detector-alert-green  { border-left: 5px solid #28a745; background: #fff; }
.detector-alert-header { display: flex; align-items: center; gap: 14px; padding: 14px 18px; flex-wrap: wrap; }
.detector-alert-icon { font-size: 22px; flex-shrink: 0; }
.detector-alert-meta { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.detector-alert-meal { font-size: 15px; color: #2c2c2c; }
.detector-alert-date { font-size: 12px; color: #999; }
.detector-alert-badge { font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; white-space: nowrap; flex-shrink: 0; }
.detector-badge-red    { background: #fde8ea; color: #dc3545; }
.detector-badge-yellow { background: #fff3cd; color: #856404; }
.detector-badge-green  { background: #d4edda; color: #155724; }
.detector-alert-detail { padding: 0 18px 14px 54px; font-size: 14px; color: #555; line-height: 1.6; }
@media (max-width: 576px) { .detector-alert-detail { padding-left: 18px; } .detector-counter { width: 90px; height: 80px; } .detector-counter-num { font-size: 26px; } }

/* ── Animated gradient background ── */
body {
    background: linear-gradient(-45deg,#e8f5e9,#e3f2fd,#e0f7fa,#f1f8e9,#e8f5e9);
    background-size: 400% 400%;
    animation: bgShift 16s ease infinite;
}
@keyframes bgShift {
    0%  { background-position: 0%   50%; }
    25% { background-position: 100% 50%; }
    50% { background-position: 100% 0%;  }
    75% { background-position: 0%   100%;}
    100%{ background-position: 0%   50%; }
}
/* ── Floating food particles ── */
.food-particles { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.food-particles span { position:absolute; bottom:-60px; opacity:0; animation:floatUp linear infinite; user-select:none; }
@keyframes floatUp {
    0%  { transform:translateY(0) rotate(0deg);    opacity:0;   }
    10% { opacity:.45; }
    90% { opacity:.25; }
    100%{ transform:translateY(-110vh) rotate(360deg); opacity:0; }
}
/* ── Table glass card ── */
.table-responsive {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(8px);
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 4px;
    transition: box-shadow .3s;
}
.table-responsive:hover { box-shadow: 0 8px 32px rgba(38,166,154,.18); }
/* ── Buttons lift + glow ── */
.boxed-btn { transition: transform .25s, box-shadow .25s !important; }
.boxed-btn:hover { transform: translateY(-3px) !important; box-shadow: 0 8px 20px rgba(242,129,35,.35) !important; }
/* ── Section title fade-in ── */
.section-title { animation: titleFadeIn .6s ease both; }
@keyframes titleFadeIn {
    from { opacity:0; transform:translateY(-16px); }
    to   { opacity:1; transform:translateY(0);     }
}
/* ── Card lift on hover ── */
.card { transition: transform .25s, box-shadow .25s; }
.card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.12); }
/* ── Form inputs focus glow ── */
.form-control:focus, .form-select:focus {
    border-color: #26a69a !important;
    box-shadow: 0 0 0 3px rgba(38,166,154,.18) !important;
    outline: none;
}
</style>
