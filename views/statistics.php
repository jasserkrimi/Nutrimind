<?php
/**
 * NutriMind — Statistics Page
 * Displays calorie consumption chart per day (read-only)
 */
session_start();
require_once '../config/Database.php';

$dates        = [];
$caloriesData = [];
$statsError   = null;
$goalKcal     = 2000;

try {
    $db   = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("
        SELECT
            m.date AS meal_date,
            ROUND(SUM(i.calories * mi.quantity / 100), 1) AS total_calories
        FROM Meal m
        INNER JOIN Meal_Ingredient mi ON mi.meal_id = m.id
        INNER JOIN Ingredient i ON i.id = mi.ingredient_id
        GROUP BY m.date
        ORDER BY m.date ASC
    ");
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $dates[]        = $row['meal_date'];
        $caloriesData[] = (float) $row['total_calories'];
    }
} catch (Exception $e) {
    $statsError = "Impossible de charger les statistiques.";
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
                <h3><span class="orange-text">Statistiques</span> Nutritionnelles</h3>
                <p>Visualisez vos calories consommées par jour.</p>
            </div>
        </div>
    </div>

    <!-- Back button -->
    <div class="row mb-4">
        <div class="col-lg-12 text-center">
            <a href="meal_list.php" class="boxed-btn"><i class="fas fa-arrow-left mr-1"></i> Retour aux repas</a>
        </div>
    </div>

    <?php if ($statsError): ?>
        <div class="row"><div class="col-lg-12">
            <div class="alert alert-warning"><?php echo htmlspecialchars($statsError); ?></div>
        </div></div>
    <?php elseif (empty($dates)): ?>
        <div class="row"><div class="col-lg-12 text-center">
            <p class="text-muted">Aucune donnée disponible. Ajoutez des repas avec des ingrédients pour voir les statistiques.</p>
        </div></div>
    <?php else: ?>

    <!-- Chart card -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="stats-card">
                <h5 class="stats-card-title">
                    <i class="fas fa-chart-line mr-2"></i>Calories consommées par jour
                </h5>
                <div class="stats-chart-wrap">
                    <canvas id="caloriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="row justify-content-center">
        <?php
            $avg  = count($caloriesData) ? round(array_sum($caloriesData) / count($caloriesData)) : 0;
            $max  = count($caloriesData) ? max($caloriesData) : 0;
            $min  = count($caloriesData) ? min($caloriesData) : 0;
            $days = count($caloriesData);
        ?>
        <div class="col-lg-2 col-md-6 mb-4">
            <div class="stat-summary-card">
                <div class="stat-summary-val"><?php echo $days; ?></div>
                <div class="stat-summary-lbl">Jours enregistrés</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-4">
            <div class="stat-summary-card">
                <div class="stat-summary-val"><?php echo $avg; ?></div>
                <div class="stat-summary-lbl">Moy. kcal/jour</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-4">
            <div class="stat-summary-card stat-max">
                <div class="stat-summary-val"><?php echo $max; ?></div>
                <div class="stat-summary-lbl">Max kcal</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-4">
            <div class="stat-summary-card stat-min">
                <div class="stat-summary-val"><?php echo $min; ?></div>
                <div class="stat-summary-lbl">Min kcal</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-4">
            <div class="stat-summary-card stat-goal">
                <div class="stat-summary-val"><?php echo $goalKcal; ?></div>
                <div class="stat-summary-lbl">Objectif kcal</div>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDates    = <?php echo json_encode($dates); ?>;
const chartCalories = <?php echo json_encode($caloriesData); ?>;
const goalKcal      = <?php echo (int) $goalKcal; ?>;

if (document.getElementById('caloriesChart') && chartDates.length > 0) {
    const ctx = document.getElementById('caloriesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartDates,
            datasets: [
                {
                    label: 'Calories consommées (kcal)',
                    data: chartCalories,
                    borderColor: '#f28123',
                    backgroundColor: 'rgba(242,129,35,0.12)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f28123',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Objectif journalier (' + goalKcal + ' kcal)',
                    data: chartDates.map(() => goalKcal),
                    borderColor: '#28a745',
                    borderWidth: 2,
                    borderDash: [8, 4],
                    pointRadius: 0,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' kcal' } }
            },
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { beginAtZero: true, title: { display: true, text: 'Calories (kcal)' } }
            }
        }
    });
}
</script>

<style>
.stats-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 18px rgba(0,0,0,.09); padding: 28px 28px 24px; }
.stats-card-title { font-size: 17px; font-weight: 700; color: #2c2c2c; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f5f5f5; }
.stats-card-title i { color: #f28123; }
.stats-chart-wrap { position: relative; height: 360px; }

/* Summary cards */
.stat-summary-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(0,0,0,.08);
    padding: 20px 12px;
    text-align: center;
    border-top: 4px solid #f28123;
}
.stat-summary-card.stat-max  { border-top-color: #dc3545; }
.stat-summary-card.stat-min  { border-top-color: #3498db; }
.stat-summary-card.stat-goal { border-top-color: #28a745; }
.stat-summary-val { font-size: 28px; font-weight: 700; color: #2c2c2c; line-height: 1; margin-bottom: 6px; }
.stat-summary-lbl { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: .4px; }

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
