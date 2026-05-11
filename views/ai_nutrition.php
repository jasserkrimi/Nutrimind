<?php
/**
 * ============================================================
 * NutriMind — AI Nutrition Analysis Page
 * ============================================================
 * READ-ONLY: This page does NOT interact with the database.
 * No meals are inserted, modified, or deleted.
 *
 * Analysis flow:
 *   1. User uploads a meal image
 *   2. JS sends base64 image  →  ../controllers/api_huggingface.php
 *      Hugging Face (nateraw/food) returns the detected food label
 *   3. JS sends food label    →  ../controllers/api_edamam.php
 *      Edamam returns calories / protein / fat / carbs
 *   4. Results rendered dynamically — NO page reload
 * ============================================================
 */
session_start();
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

<!-- ============================================================
     AI NUTRITION SECTION
============================================================ -->
<div class="product-section mt-150 mb-150">
    <div class="container">

        <!-- Page title — uses NutriMind section-title style -->
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3><span class="orange-text">Analyse</span> Nutrition IA</h3>
                    <p>Téléchargez une photo de votre repas et laissez l'IA détecter les valeurs nutritionnelles.</p>
                </div>
            </div>
        </div>

        <!-- ── Upload card ─────────────────────────────────────────────── -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6 col-md-8">
                <div class="ai-card">

                    <!-- Drop zone ─────────────────────────────────────── -->
                    <div class="ai-dropzone" id="aiDropzone"
                         role="button" tabindex="0"
                         aria-label="Zone de dépôt d'image — cliquez ou glissez une photo de repas">

                        <!-- Placeholder shown before an image is chosen -->
                        <div class="ai-placeholder" id="aiPlaceholder">
                            <i class="fas fa-camera ai-cam-icon"></i>
                            <p class="ai-ph-text">
                                Glissez une image ici<br>
                                ou <span class="ai-browse-link">cliquez pour choisir</span>
                            </p>
                            <p class="ai-ph-hint">JPG · PNG · WEBP — max 10 Mo</p>
                        </div>

                        <!-- Preview shown after an image is chosen -->
                        <img id="aiPreview" class="ai-preview d-none"
                             alt="Aperçu du repas sélectionné">

                        <!-- Real file input — invisible, covers the whole zone -->
                        <input type="file" id="aiFileInput" accept="image/*"
                               class="ai-file-input" aria-hidden="true">
                    </div>
                    <!-- /drop zone -->

                    <!-- Action buttons -->
                    <div class="text-center mt-4">
                        <button id="aiAnalyzeBtn" class="boxed-btn">
                            <i class="fas fa-microscope"></i>&nbsp; Analyser
                        </button>
                        <a href="meal_list.php" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i>&nbsp; Retour
                        </a>
                    </div>

                    <!-- Validation error (hidden by default) -->
                    <div id="aiValidationError" class="ai-validation-error d-none">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Veuillez d'abord sélectionner ou glisser une image de repas.
                    </div>

                    <!-- Loading indicator (hidden by default) -->
                    <div id="aiLoading" class="ai-loading d-none"
                         aria-live="polite" aria-busy="true">
                        <div class="ai-spinner" role="status"></div>
                        <p id="aiLoadingText" class="ai-loading-msg">Analyse en cours…</p>
                    </div>

                    <!-- Error message (hidden by default) -->
                    <div id="aiError" class="alert alert-danger mt-4 d-none" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="aiErrorText"></span>
                    </div>

                </div><!-- /.ai-card -->
            </div>
        </div><!-- /.row upload -->

        <!-- ============================================================
             RESULTS — hidden until analysis completes
        ============================================================ -->
        <div id="aiResults" class="d-none mt-5">

            <!-- Detected food banner -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center">
                    <div class="ai-food-banner">
                        <i class="fas fa-utensils mr-2"></i>
                        Aliment détecté&nbsp;:&nbsp;
                        <strong id="aiDetectedName"></strong>
                        <span class="ai-badge" id="aiConfidence"></span>
                    </div>
                </div>
            </div>

            <!-- Nutrition cards -->
            <div class="row justify-content-center">

                <!-- Calories -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="ai-nut-card ai-cal">
                        <div class="ai-nut-icon"><i class="fas fa-fire"></i></div>
                        <div class="ai-nut-val" id="aiCalories">0</div>
                        <div class="ai-nut-lbl">Calories</div>
                        <div class="ai-nut-unit">kcal</div>
                    </div>
                </div>

                <!-- Protein -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="ai-nut-card ai-prot">
                        <div class="ai-nut-icon"><i class="fas fa-dumbbell"></i></div>
                        <div class="ai-nut-val" id="aiProtein">0</div>
                        <div class="ai-nut-lbl">Protéines</div>
                        <div class="ai-nut-unit">g</div>
                    </div>
                </div>

                <!-- Fat -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="ai-nut-card ai-fat">
                        <div class="ai-nut-icon"><i class="fas fa-tint"></i></div>
                        <div class="ai-nut-val" id="aiFat">0</div>
                        <div class="ai-nut-lbl">Lipides</div>
                        <div class="ai-nut-unit">g</div>
                    </div>
                </div>

                <!-- Carbohydrates -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="ai-nut-card ai-carb">
                        <div class="ai-nut-icon"><i class="fas fa-bread-slice"></i></div>
                        <div class="ai-nut-val" id="aiCarbs">0</div>
                        <div class="ai-nut-lbl">Glucides</div>
                        <div class="ai-nut-unit">g</div>
                    </div>
                </div>

            </div><!-- /.row cards -->

            <!-- Disclaimer -->
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <p class="ai-disclaimer">
                        <i class="fas fa-info-circle mr-1"></i>
                        Valeurs estimées pour la portion détectée. Ces données sont fournies à titre indicatif.
                    </p>
                </div>
            </div>

            <!-- Analyse another image -->
            <div class="row justify-content-center mt-3">
                <div class="col-auto">
                    <button id="aiResetBtn" class="boxed-btn">
                        <i class="fas fa-redo mr-1"></i> Analyser une autre image
                    </button>
                </div>
            </div>

        </div><!-- /#aiResults -->

    </div><!-- /.container -->
</div>
<!-- ============================================================
     END AI NUTRITION SECTION
============================================================ -->

<?php include 'footer.php'; ?>

<!-- ============================================================
     JAVASCRIPT
     All API calls go through backend PHP proxies.
     No API keys or tokens are present in this file.
============================================================ -->
<script>
(function () {
    'use strict';

    /* ── DOM references ──────────────────────────────────────────── */
    const dropzone    = document.getElementById('aiDropzone');
    const placeholder = document.getElementById('aiPlaceholder');
    const fileInput   = document.getElementById('aiFileInput');
    const preview     = document.getElementById('aiPreview');
    const analyzeBtn  = document.getElementById('aiAnalyzeBtn');
    const loadingBox  = document.getElementById('aiLoading');
    const loadingMsg  = document.getElementById('aiLoadingText');
    const errorBox    = document.getElementById('aiError');
    const errorText   = document.getElementById('aiErrorText');
    const resultsBox  = document.getElementById('aiResults');
    const resetBtn    = document.getElementById('aiResetBtn');

    const elName       = document.getElementById('aiDetectedName');
    const elConfidence = document.getElementById('aiConfidence');
    const elCalories   = document.getElementById('aiCalories');
    const elProtein    = document.getElementById('aiProtein');
    const elFat        = document.getElementById('aiFat');
    const elCarbs      = document.getElementById('aiCarbs');

    /* ── State ───────────────────────────────────────────────────── */
    let selectedBase64 = null;

    /* ── UI helpers ──────────────────────────────────────────────── */

    const validationError = document.getElementById('aiValidationError');

    function showValidation() {
        validationError.classList.remove('d-none');
        // Auto-hide after 3 seconds
        setTimeout(hideValidation, 3000);
        // Shake the button
        analyzeBtn.classList.add('ai-btn-shake');
        setTimeout(() => analyzeBtn.classList.remove('ai-btn-shake'), 500);
        // Highlight the drop zone
        dropzone.classList.add('ai-dz-required');
        setTimeout(() => dropzone.classList.remove('ai-dz-required'), 1000);
    }

    function hideValidation() {
        validationError.classList.add('d-none');
    }

    function showError(msg) {
        hideLoading();
        errorText.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function hideError() {
        errorBox.classList.add('d-none');
        errorText.textContent = '';
    }

    function showLoading(msg) {
        loadingMsg.textContent = msg || 'Analyse en cours…';
        loadingBox.classList.remove('d-none');
        analyzeBtn.disabled = true;
    }

    function hideLoading() {
        loadingBox.classList.add('d-none');
        analyzeBtn.disabled = false;
    }

    /* ── Full page reset ─────────────────────────────────────────── */
    function resetPage() {
        selectedBase64 = null;
        fileInput.value = '';
        preview.src = '';
        preview.classList.add('d-none');
        placeholder.classList.remove('d-none');
        analyzeBtn.disabled = false;
        hideError();
        hideValidation();
        hideLoading();
        resultsBox.classList.add('d-none');
        elName.textContent       = '';
        elConfidence.textContent = '';
        elCalories.textContent   = '0';
        elProtein.textContent    = '0';
        elFat.textContent        = '0';
        elCarbs.textContent      = '0';
    }

    /* ── File → base64 data-URI ──────────────────────────────────── */
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload  = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Impossible de lire le fichier.'));
            reader.readAsDataURL(file);
        });
    }

    /* ── Handle a chosen / dropped file ─────────────────────────── */
    async function handleFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            showError('Veuillez sélectionner un fichier image valide (JPG, PNG, WEBP…).');
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            showError("L'image est trop volumineuse (maximum 10 Mo).");
            return;
        }

        hideError();
        hideValidation();
        resultsBox.classList.add('d-none');

        try {
            selectedBase64 = await fileToBase64(file);
            preview.src = selectedBase64;
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        } catch (err) {
            showError(err.message);
        }
    }

    /* ── Drop zone interactions ──────────────────────────────────── */

    // Click → open file picker
    dropzone.addEventListener('click', () => fileInput.click());

    // Keyboard: Enter / Space → open file picker
    dropzone.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    // File picker change
    fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) handleFile(fileInput.files[0]);
    });

    // Drag-and-drop
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('ai-dz-hover');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('ai-dz-hover');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('ai-dz-hover');
        const file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (file) handleFile(file);
    });

    /* ── Main analysis flow ──────────────────────────────────────── */
    analyzeBtn.addEventListener('click', async () => {
        // ── Validation: image must be selected ──
        if (!selectedBase64) {
            showValidation();
            return;
        }

        hideValidation();
        hideError();
        resultsBox.classList.add('d-none');

        /* ── Step 1: Hugging Face — detect food from image ── */
        showLoading('🔍 Détection de l\'aliment en cours…');

        let foodName, confidence;

        try {
            const hfRes  = await fetch('../controllers/api_huggingface.php', {
                method  : 'POST',
                headers : { 'Content-Type': 'application/json' },
                body    : JSON.stringify({ image_base64: selectedBase64 }),
            });
            const hfData = await hfRes.json();

            if (!hfRes.ok || hfData.error) {
                throw new Error(hfData.error || "Erreur lors de la détection de l'aliment.");
            }

            foodName   = hfData.food;
            confidence = hfData.confidence;

        } catch (err) {
            showError('Hugging Face : ' + err.message);
            return;
        }

        /* ── Step 2: Edamam — retrieve nutrition values ── */
        showLoading('🥗 Récupération des valeurs nutritionnelles…');

        try {
            const edRes  = await fetch('../controllers/api_edamam.php', {
                method  : 'POST',
                headers : { 'Content-Type': 'application/json' },
                body    : JSON.stringify({ food: foodName }),
            });
            const edData = await edRes.json();

            if (!edRes.ok || edData.error) {
                throw new Error(edData.error || 'Erreur lors de la récupération des données nutritionnelles.');
            }

            /* ── Step 3: Render results ── */
            hideLoading();

            elName.textContent       = capitalise(foodName);
            elConfidence.textContent = confidence + '% de confiance';

            // Animated count-up for each value
            animateValue(elCalories, edData.calories, 0);
            animateValue(elProtein,  edData.protein,  1);
            animateValue(elFat,      edData.fat,       1);
            animateValue(elCarbs,    edData.carbs,     1);

            resultsBox.classList.remove('d-none');
            resultsBox.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } catch (err) {
            showError('Edamam : ' + err.message);
        }
    });

    /* ── Reset button ────────────────────────────────────────────── */
    resetBtn.addEventListener('click', resetPage);

    /* ── Utilities ───────────────────────────────────────────────── */

    /** Capitalise the first letter of a string */
    function capitalise(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : str;
    }

    /**
     * Animate a number from 0 → target over ~800 ms (ease-out cubic).
     * @param {HTMLElement} el        Element to update
     * @param {number}      target    Final value
     * @param {number}      decimals  Decimal places
     */
    function animateValue(el, target, decimals) {
        const duration  = 800;
        const startTime = performance.now();

        (function step(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            const eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = (target * eased).toFixed(decimals);
            if (progress < 1) requestAnimationFrame(step);
        }(performance.now()));
    }

}());
</script>

<!-- ============================================================
     STYLES — scoped to this page, matching NutriMind palette
============================================================ -->
<style>
/* ── NutriMind orange accent ─────────────────────────────────────────────── */
:root {
    --nm-orange : #f28123;
    --nm-dark   : #2c2c2c;
    --nm-radius : 10px;
    --nm-shadow : 0 4px 18px rgba(0, 0, 0, .09);
}

/* ── Validation error ────────────────────────────────────────────────────── */
.ai-validation-error {
    margin-top: 10px;
    color: #dc3545;
    font-size: 13px;
    font-weight: 600;
    background: #fde8ea;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
    padding: 8px 16px;
    display: inline-block;
    animation: fadeInDown .25s ease-out;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Shake animation on button */
@keyframes ai-shake {
    0%, 100% { transform: translateX(0); }
    20%       { transform: translateX(-6px); }
    40%       { transform: translateX(6px); }
    60%       { transform: translateX(-4px); }
    80%       { transform: translateX(4px); }
}
.ai-btn-shake { animation: ai-shake .4s ease-out; }

/* Drop zone highlight when no image selected */
.ai-dz-required {
    border-color: #dc3545 !important;
    background: #fff5f5 !important;
}
.ai-dz-required .ai-cam-icon { color: #dc3545 !important; }

/* ── Upload card ─────────────────────────────────────────────────────────── */
.ai-card {
    background    : #fff;
    border-radius : var(--nm-radius);
    box-shadow    : var(--nm-shadow);
    padding       : 36px 32px;
}

/* ── Drop zone ───────────────────────────────────────────────────────────── */
.ai-dropzone {
    position        : relative;
    border          : 2px dashed #ddd;
    border-radius   : var(--nm-radius);
    min-height      : 210px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    cursor          : pointer;
    overflow        : hidden;
    transition      : border-color .25s, background .25s;
}
.ai-dropzone:hover,
.ai-dropzone:focus,
.ai-dz-hover {
    border-color : var(--nm-orange);
    background   : #fff8f2;
    outline      : none;
}

/* Placeholder content */
.ai-placeholder {
    text-align     : center;
    padding        : 20px;
    pointer-events : none;   /* clicks bubble up to the drop zone */
}
.ai-cam-icon {
    font-size     : 50px;
    color         : #ccc;
    display       : block;
    margin-bottom : 12px;
    transition    : color .25s;
}
.ai-dropzone:hover .ai-cam-icon,
.ai-dropzone:focus .ai-cam-icon,
.ai-dz-hover .ai-cam-icon { color: var(--nm-orange); }

.ai-ph-text {
    color       : #999;
    font-size   : 15px;
    margin      : 0 0 6px;
    line-height : 1.7;
}
.ai-browse-link {
    color       : var(--nm-orange);
    font-weight : 600;
}
.ai-ph-hint {
    color     : #bbb;
    font-size : 12px;
    margin    : 0;
}

/* Hidden real file input — covers the whole drop zone */
.ai-file-input {
    position : absolute;
    inset    : 0;
    opacity  : 0;
    cursor   : pointer;
    width    : 100%;
    height   : 100%;
}

/* Image preview fills the drop zone */
.ai-preview {
    width         : 100%;
    max-height    : 320px;
    object-fit    : cover;
    border-radius : calc(var(--nm-radius) - 2px);
    display       : block;
}

/* ── Loading indicator ───────────────────────────────────────────────────── */
.ai-loading {
    text-align : center;
    padding    : 24px 0 8px;
}
.ai-spinner {
    width             : 46px;
    height            : 46px;
    border            : 5px solid #f0e0d0;
    border-top-color  : var(--nm-orange);
    border-radius     : 50%;
    animation         : ai-spin .8s linear infinite;
    margin            : 0 auto 14px;
}
@keyframes ai-spin { to { transform: rotate(360deg); } }
.ai-loading-msg {
    color     : #777;
    font-size : 15px;
    margin    : 0;
}

/* ── Detected food banner ────────────────────────────────────────────────── */
.ai-food-banner {
    display       : inline-block;
    background    : linear-gradient(135deg, var(--nm-orange) 0%, #e06b10 100%);
    color         : #fff;
    border-radius : 50px;
    padding       : 12px 28px;
    font-size     : 18px;
    box-shadow    : 0 4px 14px rgba(242, 129, 35, .35);
}
.ai-badge {
    display        : inline-block;
    background     : rgba(255, 255, 255, .22);
    border-radius  : 20px;
    font-size      : 12px;
    padding        : 3px 10px;
    margin-left    : 10px;
    vertical-align : middle;
}

/* ── Nutrition cards ─────────────────────────────────────────────────────── */
.ai-nut-card {
    background    : #fff;
    border-radius : var(--nm-radius);
    box-shadow    : var(--nm-shadow);
    padding       : 28px 20px;
    text-align    : center;
    border-top    : 4px solid transparent;
    transition    : transform .25s, box-shadow .25s;
}
.ai-nut-card:hover {
    transform  : translateY(-4px);
    box-shadow : 0 8px 26px rgba(0, 0, 0, .13);
}
.ai-cal  { border-top-color: #e74c3c; }
.ai-prot { border-top-color: #3498db; }
.ai-fat  { border-top-color: #f39c12; }
.ai-carb { border-top-color: #2ecc71; }

.ai-nut-icon              { font-size: 30px; margin-bottom: 10px; }
.ai-cal  .ai-nut-icon     { color: #e74c3c; }
.ai-prot .ai-nut-icon     { color: #3498db; }
.ai-fat  .ai-nut-icon     { color: #f39c12; }
.ai-carb .ai-nut-icon     { color: #2ecc71; }

.ai-nut-val {
    font-size    : 40px;
    font-weight  : 700;
    color        : var(--nm-dark);
    line-height  : 1;
    margin-bottom: 4px;
}
.ai-nut-lbl {
    font-size      : 14px;
    color          : #555;
    font-weight    : 600;
    text-transform : uppercase;
    letter-spacing : .5px;
}
.ai-nut-unit {
    font-size  : 13px;
    color      : #aaa;
    margin-top : 2px;
}

/* ── Disclaimer ──────────────────────────────────────────────────────────── */
.ai-disclaimer {
    color      : #aaa;
    font-size  : 13px;
    margin-top : 8px;
}

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 576px) {
    .ai-card        { padding: 22px 14px; }
    .ai-nut-val     { font-size: 30px; }
    .ai-food-banner { font-size: 14px; padding: 10px 16px; }
}

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
