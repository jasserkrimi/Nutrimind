<?php ob_start(); ?>

<!-- SECTION VIDÉO ANIMÉE (GANT DE BOXE) -->
<div class="video-scroll-wrapper" style="width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; height: 250vh; margin-top: -150px; margin-bottom: 100px; background: #000;">
    <div class="video-sticky-container" style="position: sticky; top: 0; height: 100vh; overflow: hidden; display: flex; justify-content: center; align-items: center;">
        
        <!-- Le vrai rendu ultra-fluide se fera sur ce Canvas -->
        <canvas id="hero-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.7;"></canvas>
        
        <!-- Vidéo cachée utilisée uniquement par le navigateur pour extraire les images en mémoire -->
        <video id="boxe-video" muted playsinline preload="auto" style="display: none;">
            <source src="views/assets_front/videos/boxing-gloves.mp4" type="video/mp4">
        </video>
        
        <div id="loading-indicator" style="position: absolute; z-index: 3; color: #F28123; font-size: 1.2rem; background: rgba(0,0,0,0.8); padding: 10px 20px; border-radius: 20px;">
            <i class="fas fa-spinner fa-spin"></i> Préparation de la haute fluidité... <span id="load-progress">0</span>%
        </div>

        <div id="hero-text" style="position: relative; z-index: 2; text-align: center; color: white; background: rgba(0,0,0,0.4); padding: 40px; border-radius: 10px; border: 2px solid #F28123; backdrop-filter: blur(5px); opacity: 0; transition: opacity 1s ease;">
            <h1 style="font-size: 4rem; text-transform: uppercase; letter-spacing: 5px; color: #FFF; margin-bottom: 10px;">
                <span style="color: #F28123;">Dépassez</span> vos limites
            </h1>
            <p style="font-size: 1.2rem; letter-spacing: 2px;">Scrollez pour découvrir notre univers</p>
        </div>
    </div>
</div>

<!-- SECTION POURQUOI LE SPORT -->
<div class="container mt-5 mb-5">
    <div class="row align-items-center" style="background: rgba(20,20,20,0.8); padding: 50px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border-left: 5px solid #F28123;">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 style="color: #FFF; font-size: 2.5rem; font-weight: 700; margin-bottom: 20px;">
                Pourquoi le <span style="color: #F28123;">Sport</span> est essentiel ?
            </h2>
            <p style="color: #DDD; font-size: 1.1rem; line-height: 1.8;">
                Le sport ne se résume pas à l'effort physique. C'est une discipline qui forge le mental, améliore la santé globale et libère des endorphines. 
                Que vous cherchiez à perdre du poids, à prendre de la masse musculaire ou simplement à vous sentir mieux dans votre peau, chaque entraînement vous rapproche de votre meilleure version.
            </p>
            <div class="mt-4">
                <div class="d-flex align-items-center mb-3">
                    <div style="background: #F28123; color: #FFF; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h4 style="color: #FFF; margin: 0; font-size: 1.2rem;">Santé de fer</h4>
                        <p style="color: #AAA; margin: 0; font-size: 0.95rem;">Renforce le cœur et le système immunitaire</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div style="background: #F28123; color: #FFF; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div>
                        <h4 style="color: #FFF; margin: 0; font-size: 1.2rem;">Mental d'acier</h4>
                        <p style="color: #AAA; margin: 0; font-size: 0.95rem;">Réduit le stress et améliore la concentration</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div style="background: #F28123; color: #FFF; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 15px;">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div>
                        <h4 style="color: #FFF; margin: 0; font-size: 1.2rem;">Énergie décuplée</h4>
                        <p style="color: #AAA; margin: 0; font-size: 0.95rem;">Augmente votre vitalité au quotidien</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=800" alt="Motivation Sport" style="width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(242, 129, 35, 0.3); transform: perspective(1000px) rotateY(-5deg); transition: transform 0.5s;" onmouseover="this.style.transform='perspective(1000px) rotateY(0deg)'" onmouseout="this.style.transform='perspective(1000px) rotateY(-5deg)'">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">	
            <h3><span class="orange-text">Nos</span> Activités</h3>
            <p>Découvrez notre catalogue complet d'activités sportives.</p>
        </div>
    </div>
</div>

<div class="row">
    <?php 
    $images = [
        'Force' => 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=800',
        'Perte de poids' => 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=800',
        'Être musclé' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=800',
        'Cardio' => 'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?q=80&w=800',
        'Souplesse' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=800',
        'default' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=800'
    ];
    if (empty($activites)): ?>
        <div class="col-12 text-center">
            <p>Aucune activité disponible pour le moment.</p>
        </div>
    <?php else: 
        foreach ($activites as $activite): 
            $imgUrl = isset($images[$activite['categorie']]) ? $images[$activite['categorie']] : $images['default'];
    ?>
        <div class="col-lg-4 col-md-6 text-center">
            <div class="single-product-item">
                <div class="product-image">
                    <a href="index.php?c=home&action=exercices&id=<?= $activite['id'] ?>">
                        <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($activite['nom']) ?>" style="height: 250px; object-fit: cover; border-radius: 5px;">
                    </a>
                </div>
                <h3><?= htmlspecialchars($activite['nom']) ?></h3>
                <p class="product-price"><span><?= htmlspecialchars($activite['categorie']) ?></span></p>
                <a href="index.php?c=home&action=exercices&id=<?= $activite['id'] ?>" class="cart-btn"><i class="fas fa-dumbbell"></i> Voir Exercices</a>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<!-- SCRIPTS POUR L'ANIMATION GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    gsap.registerPlugin(ScrollTrigger);

    const video = document.getElementById('boxe-video');
    const canvas = document.getElementById('hero-canvas');
    const ctx = canvas.getContext('2d');
    const loadingIndicator = document.getElementById('loading-indicator');
    const progressText = document.getElementById('load-progress');
    const heroText = document.getElementById('hero-text');

    let frames = [];
    let base64Frames = [];
    const CACHE_KEY = 'boxe_video_frames';

    function initScrollTrigger() {
        loadingIndicator.style.display = 'none';
        heroText.style.opacity = '1';

        // Lier le Scroll au tableau d'images
        ScrollTrigger.create({
            trigger: ".video-scroll-wrapper",
            start: "top top",
            end: "bottom bottom",
            scrub: 0.5,
            onUpdate: (self) => {
                let frameIndex = Math.floor(self.progress * (frames.length - 1));
                if (frames[frameIndex] && frames[frameIndex].complete) {
                    ctx.drawImage(frames[frameIndex], 0, 0, canvas.width, canvas.height);
                }
            }
        });
        
        // Afficher la première image
        if(frames[0] && frames[0].complete) {
            ctx.drawImage(frames[0], 0, 0, canvas.width, canvas.height);
        } else if (frames[0]) {
            frames[0].onload = () => ctx.drawImage(frames[0], 0, 0, canvas.width, canvas.height);
        }
    }

    // Tenter de récupérer le cache
    let cachedData = sessionStorage.getItem(CACHE_KEY);
    
    if (cachedData) {
        // Le cache existe ! On saute l'extraction
        try {
            base64Frames = JSON.parse(cachedData);
            canvas.width = 1280; // Taille par défaut approximative
            canvas.height = 720;
            
            // Reconstruire les objets Image
            for (let i = 0; i < base64Frames.length; i++) {
                let img = new Image();
                img.src = base64Frames[i];
                frames.push(img);
            }
            
            // Lancer directement l'animation
            initScrollTrigger();
            return; // On arrête là
        } catch (e) {
            console.warn("Cache corrompu, on recalcule.", e);
            sessionStorage.removeItem(CACHE_KEY);
        }
    }

    // SI AUCUN CACHE : Extraction de la vidéo
    video.addEventListener('loadeddata', async function() {
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;
        
        let duration = video.duration;
        if (!duration || isNaN(duration)) duration = 5;
        
        const fps = 12; // 12 fps pour réduire la taille et garantir que ça rentre dans la mémoire cache (5Mo limite)
        let totalFrames = Math.floor(duration * fps);
        let timeStep = duration / totalFrames;

        for (let i = 0; i <= totalFrames; i++) {
            video.currentTime = i * timeStep;
            await new Promise(resolve => {
                video.addEventListener('seeked', resolve, { once: true });
            });
            
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Compresser fortement (0.4) pour le cache Session
            let base64Src = canvas.toDataURL("image/jpeg", 0.4);
            base64Frames.push(base64Src);
            
            let img = new Image();
            img.src = base64Src;
            frames.push(img);
            
            progressText.innerText = Math.round((i / totalFrames) * 100);
        }

        // Sauvegarder dans le cache du navigateur pour ne plus jamais recalculer
        try {
            sessionStorage.setItem(CACHE_KEY, JSON.stringify(base64Frames));
        } catch(e) {
            console.warn("Le cache est plein, les images ne seront pas mémorisées.", e);
        }

        initScrollTrigger();
    });

    video.load();
});
</script>

<?php
$content = ob_get_clean();
require 'views/layout_front.php';
?>
