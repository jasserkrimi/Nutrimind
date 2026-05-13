<?php
/**
 * seed_exercices_sport.php — Peuple la DB nutrimind avec de vrais exercices
 * Accéder via : http://localhost/nutrimind_int/seed_exercices_sport.php
 */
require_once 'config.php';
require_once 'models/Database.php';

$db = Database::getInstance()->getConnection();

// Vérifier si les tables existent
try {
  $db->query("SELECT 1 FROM activites_sportives LIMIT 1");
  $db->query("SELECT 1 FROM exercices LIMIT 1");
} catch (Exception $e) {
  die("<h2 style='color:red;font-family:sans-serif'>❌ Tables manquantes. Vérifiez que la DB 'nutrimind' contient les tables activites_sportives et exercices.</h2><p style='font-family:sans-serif'>Erreur: ".$e->getMessage()."</p>");
}

// Vider les données sport existantes
$db->exec("DELETE FROM exercices");
$db->exec("DELETE FROM activites_sportives");

// ============================================================
// ACTIVITÉS
// ============================================================
$activites = [
  ['HIIT', 'Entraînement fractionné à haute intensité pour brûler un maximum de calories en un minimum de temps.', 'Perte de poids'],
  ['Musculation', 'Travail avec poids libres et machines pour développer la masse musculaire et la force.', 'Être musclé'],
  ['Yoga', 'Discipline qui associe postures, respiration et méditation pour équilibrer corps et esprit.', 'Autre'],
  ['Course à pied', 'Activité cardio-vasculaire d\'endurance accessible à tous pour améliorer le souffle.', 'Cardio'],
  ['Natation', 'Sport complet sollicitant 90% des muscles du corps sans impact articulaire.', 'Cardio'],
  ['Cyclisme', 'Activité d\'endurance qui renforce les jambes et améliore l\'endurance cardiovasculaire.', 'Cardio'],
  ['Force Athlétique', 'Méthodologie pour augmenter la force absolue sur les mouvements fondamentaux.', 'Force'],
  ['Pilates', 'Renforcement du centre du corps (core) pour améliorer la posture et la stabilité.', 'Autre'],
];

$stmtAct = $db->prepare("INSERT INTO activites_sportives (nom, description, categorie) VALUES (:nom, :desc, :cat)");
$actIds = [];
foreach ($activites as $act) {
  $stmtAct->execute(['nom'=>$act[0], 'desc'=>$act[1], 'cat'=>$act[2]]);
  $actIds[$act[0]] = $db->lastInsertId();
}

// ============================================================
// EXERCICES
// ============================================================
$exercices = [
  // HIIT
  ['Burpees', 'Mouvement complet en 4 temps : position debout → accroupi → planche → saut. Sollicite le corps entier et fait monter le rythme cardiaque très rapidement.', 'dZgVxmf6jkA', 'Avancé', 'HIIT'],
  ['Mountain Climbers', 'En position de planche haute, amenez alternativement les genoux vers la poitrine en accélérant. Excellent pour le core et le cardio.', 'nmwgirgXLYM', 'Intermédiaire', 'HIIT'],
  ['Jump Squats', 'Réalisez un squat complet puis explosez vers le haut en sautant. Développe la puissance des membres inférieurs.', 'A-cFYWvaHr0', 'Intermédiaire', 'HIIT'],
  ['High Knees', 'Courez sur place en montant les genoux au niveau des hanches. Bras actifs, dos droit. Intensité cardio maximale.', 'tx5LvBdBCPc', 'Débutant', 'HIIT'],
  ['Box Jumps', 'Sautez sur une boîte avec les deux pieds simultanément. Développe l\'explosivité et la puissance.', 'NBY9-kTuHEk', 'Avancé', 'HIIT'],
  ['Jumping Jacks', 'Écartez jambes et bras simultanément au-dessus de la tête puis revenez. Parfait pour l\'échauffement cardio.', 'iSSAk4XCsRA', 'Débutant', 'HIIT'],

  // Musculation
  ['Pompes (Push-ups)', 'En planche, fléchissez les coudes pour descendre la poitrine au sol puis poussez. Travaille pectoraux, triceps et épaules.', 'IODxDxX7oi4', 'Débutant', 'Musculation'],
  ['Tractions (Pull-ups)', 'Suspendez-vous à une barre et tirez jusqu\'à ce que le menton dépasse. Grand dorsal et biceps fortement sollicités.', 'eGo4IYlbE5g', 'Avancé', 'Musculation'],
  ['Développé couché (Bench Press)', 'Abaissez une barre chargée jusqu\'à la poitrine puis poussez en extension. Exercice roi pour les pectoraux.', 'rT7DgCr-3pg', 'Intermédiaire', 'Musculation'],
  ['Squat barre', 'Avec une barre sur les trapèzes, fléchissez les genoux jusqu\'au parallèle. Le roi des exercices de jambes.', 'aclHkVaku9U', 'Intermédiaire', 'Musculation'],
  ['Soulevé de terre (Deadlift)', 'Soulevez une barre du sol jusqu\'en position debout dos droit. Sollicite toute la chaîne postérieure.', 'op9kVnSso6Q', 'Avancé', 'Musculation'],
  ['Curl biceps haltère', 'Fléchissez le coude en amenant l\'haltère vers l\'épaule. Cible directement le biceps brachial.', 'ykJmrZ5v0Oo', 'Débutant', 'Musculation'],
  ['Dips triceps', 'Sur barres parallèles, fléchissez les coudes pour descendre puis poussez. Excellent pour les triceps.', '2z8JmcrK-As', 'Intermédiaire', 'Musculation'],

  // Yoga
  ['Salutation au Soleil', 'Enchaînement de 12 postures qui échauffe et étire l\'ensemble du corps. Énergie et clarté mentale garanties.', 'v7AYKMP6rOE', 'Débutant', 'Yoga'],
  ['Guerrier II (Virabhadrasana II)', 'Jambes écartées, genou fléchi, bras tendus horizontalement. Renforce les jambes et ouvre les hanches.', 'VpWMgLFd5qE', 'Débutant', 'Yoga'],
  ['Chien tête en bas (Adho Mukha)', 'V inversé avec le corps. Étire les ischio-jambiers, mollets et colonne vertébrale.', 'EC7RGZoUoiY', 'Débutant', 'Yoga'],
  ['Cobra (Bhujangasana)', 'Allongé ventre au sol, levez le torse en gardant les hanches au sol. Étire les abdominaux et renforce le dos.', '2RCFbIrGmrw', 'Débutant', 'Yoga'],
  ['Arbre (Vrikshasana)', 'Tenez-vous sur un pied, l\'autre sur la cuisse. Améliore l\'équilibre et la concentration.', 'wdln9qWYloU', 'Intermédiaire', 'Yoga'],

  // Course à pied
  ['Footing léger (Easy Run)', 'Course à allure conversationnelle 20-45 min. Développe l\'endurance de base et la capacité cardio-vasculaire.', 'flp62R2v3vs', 'Débutant', 'Course à pied'],
  ['Intervalles 400m', 'Alternez sprints de 400m à 85-90% VMA avec récupérations jogging. Améliore la vitesse maximale aérobie.', 'YAp_vT1eXrI', 'Avancé', 'Course à pied'],
  ['Côtes (Hill Repeats)', 'Sprints en montée répétés, retour en marchant. Renforce les jambes et améliore la puissance.', 'k68XBZ1z3VA', 'Intermédiaire', 'Course à pied'],
  ['Course longue (Long Run)', 'Sortie de 60-120 min à allure lente. Développe l\'endurance fondamentale.', 'tjx8AFQV1Rs', 'Intermédiaire', 'Course à pied'],

  // Natation
  ['Crawl (Nage libre)', 'Nage la plus rapide et complète. Coordination bras et battements alternés. Dos, épaules et cardio.', 'jkPGH_Z0Zio', 'Intermédiaire', 'Natation'],
  ['Brasse', 'Nage symétrique douce pour les articulations. Mouvement circulaire des bras, impulsion simultanée des jambes.', 'am8a9rNM0Es', 'Débutant', 'Natation'],
  ['Dos crawlé', 'Nage sur le dos. Excellente pour la posture et soulager les douleurs lombaires.', 'Z87LS64OFrY', 'Intermédiaire', 'Natation'],
  ['Papillon (Butterfly)', 'La nage la plus exigeante. Ondulation du corps avec propulsion simultanée des bras.', 'BxjPnMWPaKI', 'Avancé', 'Natation'],

  // Cyclisme
  ['Sortie endurance vélo', 'Sortie 60-90 min à rythme modéré (60-70% FC max). Développe l\'endurance de base.', 'fTQkC1RQvfw', 'Débutant', 'Cyclisme'],
  ['Intervalles vélo (HIIT)', 'Alternez 1 min à 90-95% FC max et 2 min de récupération. Améliore la VO2max.', 'sGS-vaNR3Yk', 'Avancé', 'Cyclisme'],
  ['Côtes vélo', 'Montées répétées en danseuse. Renforce les quadriceps et améliore la puissance.', 'V5kB6-4ABMQ', 'Intermédiaire', 'Cyclisme'],

  // Force Athlétique
  ['Squat (Powerlifting)', 'Squat profond en charge maximale, descente en dessous du parallèle. Force absolue des membres inférieurs.', 'U5ZrpAHa5sY', 'Avancé', 'Force Athlétique'],
  ['Deadlift Sumo', 'Pieds très écartés, prise étroite. Réduit le stress lombaire, cible adducteurs et fessiers.', 'dSmFQLfQlUA', 'Avancé', 'Force Athlétique'],
  ['Développé militaire (OHP)', 'Poussez une barre de hauteur d\'épaules jusqu\'en extension complète. Épaules, triceps et core.', 'CnBmiBqp-AI', 'Avancé', 'Force Athlétique'],

  // Pilates
  ['Le Cent (The Hundred)', 'Allongé, jambes à 45°, pompez les bras 100 fois. Renforce les abdominaux profonds.', 'BoKvIiGEBOY', 'Intermédiaire', 'Pilates'],
  ['Roulade (Roll-Up)', 'Déroulez vertèbre par vertèbre de couché à assis. Mobilise la colonne et renforce les abdos.', 'vz6kVY2p50g', 'Intermédiaire', 'Pilates'],
  ['Pont (Bridge)', 'Allongé, genoux fléchis, poussez les hanches vers le plafond. Renforce fessiers et stabilise le bassin.', '1jFBBBbMsD4', 'Débutant', 'Pilates'],
  ['Planche latérale (Side Plank)', 'Appui sur un avant-bras, corps en ligne droite. Renforce les obliques et la stabilité latérale.', 'wqzrb7C7UA8', 'Intermédiaire', 'Pilates'],
];

$stmtEx = $db->prepare("INSERT INTO exercices (nom, description, url_video, difficulte, activite_id) VALUES (:nom, :desc, :vid, :diff, :act_id)");
$count = 0;
foreach ($exercices as $ex) {
  if (!isset($actIds[$ex[4]])) continue;
  $stmtEx->execute(['nom'=>$ex[0], 'desc'=>$ex[1], 'vid'=>$ex[2], 'diff'=>$ex[3], 'act_id'=>$actIds[$ex[4]]]);
  $count++;
}

echo "<!DOCTYPE html><html><head><style>body{font-family:sans-serif;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{background:#1e293b;border-radius:20px;padding:40px;max-width:500px;text-align:center;border:1px solid rgba(99,102,241,.3)}h2{color:#4ade80;font-size:2rem;margin-bottom:10px}p{color:#94a3b8}a{color:#818cf8;text-decoration:none;font-weight:600}</style></head><body><div class='box'><h2>✅ Seed terminé !</h2><p><strong style='color:#fff;font-size:1.2rem'>$count exercices</strong> insérés dans ".count($actIds)." activités.</p><br><a href='/nutrimind_int/?c=home'>→ Aller au site NutriMind</a></div></body></html>";
?>
