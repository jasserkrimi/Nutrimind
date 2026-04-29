<?php ob_start(); ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">	
            <h3><span class="orange-text">Mon</span> Planning</h3>
            <p>Voici l'emploi du temps de vos séances sportives de la semaine.</p>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($calendrier as $jour => $seancesJour): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="single-product-item" style="padding: 20px; text-align: left; height: 100%;">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid #F28123; padding-bottom: 10px; display: inline-block;">
                    <i class="fas fa-calendar-day" style="color: #F28123;"></i> <?= htmlspecialchars($jour) ?>
                </h3>
                
                <?php if (empty($seancesJour)): ?>
                    <p style="color: #999; font-style: italic;">Repos ! Aucune séance.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach ($seancesJour as $seance): ?>
                            <li style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #F28123;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                    <span style="font-weight: bold; color: #333;">
                                        <i class="far fa-clock"></i> <?= date('H:i', strtotime($seance['heure_debut'])) ?>
                                    </span>
                                    <span style="font-size: 11px; padding: 3px 8px; border-radius: 10px; background: #eee;">
                                        <?= htmlspecialchars($seance['statut']) ?>
                                    </span>
                                </div>
                                <div style="font-size: 16px; font-weight: 600; color: #051922;">
                                    <?= htmlspecialchars($seance['activite_nom']) ?>
                                </div>
                                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                    <?= htmlspecialchars($seance['categorie']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row mb-5 mt-4">
    <div class="col-12 text-center">
        <a href="index.php?c=seance" class="boxed-btn"><i class="fas fa-edit"></i> Modifier le planning (Admin)</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'views/layout_front.php';
?>
