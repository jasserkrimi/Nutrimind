<?php ob_start(); ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">	
            <h3><span class="orange-text">Exercices</span> de <?= htmlspecialchars($activite['nom']) ?></h3>
            <p><?= htmlspecialchars($activite['description'] ?? '') ?></p>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12 text-center">
        <a href="index.php?c=home" class="boxed-btn"><i class="fas fa-arrow-left"></i> Retour aux activités</a>
    </div>
</div>

<div class="row">
    <?php if (empty($exercices)): ?>
        <div class="col-12 text-center">
            <p>Aucun exercice disponible pour cette activité.</p>
        </div>
    <?php else: 
        foreach ($exercices as $exercice): 
    ?>
        <div class="col-lg-4 col-md-6 text-center">
            <div class="single-product-item">
                <div class="product-image" style="background: linear-gradient(135deg, #E8F0DC 0%, #F8FAF5 100%); height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 5px; overflow: hidden;">
                    <video class="exercise-video" style="width: 100%; height: 100%; object-fit: cover;" muted playsinline>
                        <source src="views/assets_front/videos/boxing-gloves.mp4" type="video/mp4">
                    </video>
                </div>
                <h3><?= htmlspecialchars($exercice['nom']) ?></h3>
                <p class="product-price" style="font-size: 16px;"><span>Difficulté</span> <?= htmlspecialchars($exercice['difficulte'] ?? 'N/A') ?></p>
                <p><?= htmlspecialchars($exercice['description'] ?? 'Aucune description') ?></p>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'views/layout_front.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videos = document.querySelectorAll('.exercise-video');
    
    videos.forEach(video => {
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;
            const delta = currentScrollY - lastScrollY;
            
            // Récupère la durée et la position actuelle
            const duration = video.duration;
            if (duration) {
                let newTime = video.currentTime + (delta * 0.001);
                newTime = Math.max(0, Math.min(duration, newTime));
                video.currentTime = newTime;
            }
            
            lastScrollY = currentScrollY;
        });
    });
});
</script>
