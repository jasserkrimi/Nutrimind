<?php
// controllers/HomeController.php
require_once 'models/ActiviteSportive.php';
require_once 'models/Exercice.php';
require_once 'models/Database.php';

class HomeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
        $activites = $stmt->fetchAll();
        require_once 'views/sport/home.php';
    }

    public function exercices() {
        $activite_id = $_GET['id'] ?? null;
        if (!$activite_id) {
            header('Location: index.php?c=home');
            exit();
        }

        $stmt = $this->db->prepare("SELECT * FROM activites_sportives WHERE id = :id");
        $stmt->execute(['id' => $activite_id]);
        $activite = $stmt->fetch();
        
        if (!$activite) {
            die("Activité introuvable.");
        }

        $stmtEx = $this->db->prepare("SELECT * FROM exercices WHERE activite_id = :activite_id");
        $stmtEx->execute(['activite_id' => $activite_id]);
        $exercices = $stmtEx->fetchAll();
        
        require_once 'views/sport/exercices.php';
    }

    public function planning() {
        // Obtenir toutes les séances programmées
        $stmt = $this->db->query("
            SELECT sp.*, a.nom as activite_nom, a.categorie 
            FROM seances_programmees sp
            JOIN activites_sportives a ON sp.activite_id = a.id
            ORDER BY FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut ASC
        ");
        $seances = $stmt->fetchAll();

        // Organiser les séances par jour pour le calendrier
        $calendrier = [
            'Lundi' => [], 'Mardi' => [], 'Mercredi' => [], 
            'Jeudi' => [], 'Vendredi' => [], 'Samedi' => [], 'Dimanche' => []
        ];

        foreach ($seances as $seance) {
            $calendrier[$seance['jour_semaine']][] = $seance;
        }

        require_once 'views/sport/planning.php';
    }

    // Chatbot API pour recommander des activités (Frontend)
    public function chatbot() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userMessage = trim($data['message'] ?? '');

            if (empty($userMessage)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'empty',
                    'message' => 'Écris quelque chose ! 😊'
                ]);
                exit();
            }

            // Vérifier si c'est une salutation
            $greetingResponse = $this->checkGreeting($userMessage);
            if ($greetingResponse) {
                echo json_encode([
                    'success' => false,
                    'isGreeting' => true,
                    'message' => $greetingResponse
                ]);
                exit();
            }

            // Récupérer toutes les activités
            $stmt = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
            $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Analyser le message et recommander des activités
            $recommendations = $this->analyzeAndRecommend($userMessage, $activites);

            // Gérer les erreurs de compréhension
            if (isset($recommendations['error'])) {
                $errorType = $recommendations['error'];
                
                if ($errorType === 'message_trop_court') {
                    echo json_encode([
                        'success' => false,
                        'error' => 'short_message',
                        'message' => '📝 Ton message est trop court. Décris mieux ton besoin ! Par exemple : "Je veux perdre du poids" ou "Je suis stressé et je veux me détendre"'
                    ]);
                } else if ($errorType === 'message_incompris') {
                    echo json_encode([
                        'success' => false,
                        'error' => 'not_understood',
                        'message' => '🤔 Je n\'ai pas bien compris ce que tu demandes. Essaie de me dire tes objectifs (perdre du poids, te muscler, te détendre...) ou tes problèmes (stress, douleur, fatigue...)'
                    ]);
                }
                exit();
            }

            // Si pas de recommandations après analyse
            if (empty($recommendations)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'no_match',
                    'message' => '😕 Aucune activité correspond à ta description. Reformule ta demande avec plus de détails !'
                ]);
                exit();
            }

            // Ajouter les exercices pour chaque activité recommandée
            foreach ($recommendations as $key => $activity) {
                $stmtExercices = $this->db->prepare("SELECT * FROM exercices WHERE activite_id = :activite_id LIMIT 3");
                $stmtExercices->execute(['activite_id' => $activity['id']]);
                $recommendations[$key]['exercices'] = $stmtExercices->fetchAll(PDO::FETCH_ASSOC);
            }

            // Recommandations trouvées !
            echo json_encode([
                'success' => true,
                'recommendations' => $recommendations,
                'message' => 'Recommandations trouvées !'
            ]);
        }
        exit();
    }

    // Nettoyer et normaliser le message (fautes d'orthographe, accents, espacements)
    private function normalizeMessage($message) {
        // Convertir en minuscules
        $message = strtolower(trim($message));
        
        // Supprimer les accents
        $accents = ['à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a',
                    'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
                    'î' => 'i', 'ï' => 'i',
                    'ô' => 'o', 'ö' => 'o', 'ó' => 'o',
                    'ù' => 'u', 'û' => 'u', 'ü' => 'u',
                    'ç' => 'c', 'ñ' => 'n'];
        $message = strtr($message, $accents);
        
        // Supprimer les caractères spéciaux except spaces
        $message = preg_replace('/[^a-z0-9\s\']/', ' ', $message);
        
        // Normaliser les espaces mal placés (d ' accord -> d accord)
        $message = preg_replace('/\s+/', ' ', $message);
        $message = str_replace("' ", " ", $message);
        $message = str_replace(" '", " ", $message);
        
        return trim($message);
    }

    // Vérifier si deux mots sont similaires malgré les fautes
    private function isSimilarWord($word1, $word2, $maxDistance = 2) {
        $word1 = $this->normalizeMessage($word1);
        $word2 = $this->normalizeMessage($word2);
        
        // Si identique
        if ($word1 === $word2) return true;
        
        // Calcul de la distance de Levenshtein
        $distance = levenshtein($word1, $word2);
        
        return $distance <= $maxDistance;
    }

    // Chercher un mot même avec des fautes
    private function findSimilarKeyword($message, $keyword, $maxDistance = 2) {
        $message = $this->normalizeMessage($message);
        $keyword = $this->normalizeMessage($keyword);
        
        // Vérifier si le mot-clé est présent exactement
        if (strpos($message, $keyword) !== false) {
            return true;
        }
        
        // Vérifier par mot
        $messageWords = explode(' ', $message);
        $keywordWords = explode(' ', $keyword);
        
        // Si le mot-clé est sur plusieurs mots (ex: "perdre poids")
        if (count($keywordWords) > 1) {
            // Chercher si TOUS les mots du keyword sont présents dans le message
            $allWordsFound = true;
            foreach ($keywordWords as $keywordWord) {
                $wordFound = false;
                foreach ($messageWords as $messageWord) {
                    if ($this->isSimilarWord($messageWord, $keywordWord, $maxDistance)) {
                        $wordFound = true;
                        break;
                    }
                }
                if (!$wordFound) {
                    $allWordsFound = false;
                    break;
                }
            }
            return $allWordsFound;
        } else {
            // Mot-clé unique - chercher dans les mots du message
            foreach ($messageWords as $word) {
                if ($this->isSimilarWord($word, $keyword, $maxDistance)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    // Vérifier et répondre aux salutations
    private function checkGreeting($message) {
        $normalizedMessage = $this->normalizeMessage($message);
        
        // Détecter la langue (français ou anglais)
        $language = $this->detectLanguage($message);
        
        // Dictionnaire FRANÇAIS
        $greetingsFR = [
            'bonjour' => ['Bonjour ! 👋 Comment puis-je t\'aider aujourd\'hui ? Dis-moi tes objectifs sportifs !', 'Salut ! 😊 Qu\'est-ce que tu cherches ? Décris-moi ton besoin !', 'Coucou ! 🎉 Prêt à trouver ton activité parfaite ?'],
            'bonsoir' => ['Bonsoir ! 🌙 T\'es prêt pour un entraînement ? Parlons sport !', 'Salut ! 😊 Qu\'est-ce que je peux faire pour toi ce soir ?'],
            'salut' => ['Salut ! 👋 Qu\'est-ce que tu cherches ? Dis-moi tes objectifs !', 'Coucou ! 😊 Prêt à trouver l\'activité parfaite ?', 'Yo ! 🎯 Parlons de tes objectifs sportifs !'],
            'merci' => ['De rien ! 😊 C\'est mon job !', 'Avec plaisir ! 🤗 Besoin d\'autre chose ?', 'Content de t\'aider ! 💪'],
            'oui' => ['Super ! 🎉 Alors raconte-moi ce que tu cherches !', 'Cool ! 🔥 Tes objectifs sportifs ?', 'Nickel ! 💪 Vas-y !'],
            'non' => ['Pas de souci ! 😊 Je suis là quand tu auras besoin.', 'D\'accord ! À bientôt ! 👋', 'Pas de problème, bon courage ! 🏃'],
            'aid moi' => ['Bien sûr ! 💪 Décris-moi ce que tu cherches et je trouve l\'activité parfaite pour toi !', 'Avec plaisir ! 😊 Parle-moi de tes objectifs sportifs !'],
            'je veux' => ['Excellent ! 💪 Dis-moi plus de détails et je recommande l\'activité parfaite !', 'Super ! 🎯 Qu\'est-ce que tu cherches exactement ?'],
            'j ai' => ['D\'accord ! 🎯 Décris-moi ton problème et je vais t\'aider !', 'Je comprends ! 💪 Parlons-en !'],
            'en francais' => ['Bien sûr ! 🇫🇷 Je te réponds en français ! Dis-moi tes objectifs sportifs !', 'Parfait ! 🇫🇷 Parlons en français ! Qu\'est-ce que tu cherches ?'],
        ];
        
        // Dictionnaire ANGLAIS
        $greetingsEN = [
            'hello' => ['Hello ! 👋 Tell me what you\'re looking for!', 'Hey ! 😊 What are you searching for?'],
            'hi' => ['Hi there ! 👋 Tell me your goals!', 'Hey ! 😊 What are you looking for?'],
            'hey' => ['Hey ! 👋 What can I help you with?', 'Hi ! 😊 Tell me your goals!'],
            'thanks' => ['You\'re welcome ! 😊', 'Happy to help ! 🤗', 'Anytime ! 💪'],
            'thank you' => ['You\'re welcome ! 😊 Anything else?', 'Happy to help ! 🤗'],
            'yes' => ['Great ! 🎉 Tell me what you\'re looking for!', 'Cool ! 🔥 Your goals?', 'Nice ! 💪 Go ahead!'],
            'no' => ['No problem ! 😊 I\'m here when you need me.', 'OK ! See you later ! 👋', 'No worries, good luck ! 🏃'],
            'help' => ['Of course ! 💪 Tell me what you\'re looking for and I\'ll find the perfect activity!', 'Sure ! 😊 Tell me your fitness goals!'],
            'in english' => ['Of course ! 🇺🇸 I\'ll respond in English! Tell me your fitness goals!', 'Perfect ! 🇺🇸 Let\'s talk in English! What are you looking for?'],
        ];

        // Chercher d'abord dans la langue détectée
        $greetings = ($language === 'en') ? $greetingsEN : $greetingsFR;
        
        foreach ($greetings as $keyword => $responses) {
            if ($this->findSimilarKeyword($normalizedMessage, $keyword, 2)) {
                $randomKey = array_rand($responses);
                return $responses[$randomKey];
            }
        }

        // Si aucune correspondance, chercher dans l'autre langue mais moins prioritaire
        $greetings = ($language === 'en') ? $greetingsFR : $greetingsEN;
        
        foreach ($greetings as $keyword => $responses) {
            if ($this->findSimilarKeyword($normalizedMessage, $keyword, 2)) {
                $randomKey = array_rand($responses);
                return $responses[$randomKey];
            }
        }

        return null;
    }

    // Détecter la langue du message (français ou anglais)
    private function detectLanguage($message) {
        $message = strtolower($message);
        
        // Mots français courants
        $frenchWords = ['je', 'tu', 'il', 'du', 'de', 'pour', 'et', 'les', 'des', 'dans', 'un', 'une', 'à', 'au', 'que', 'qui', 'mon', 'ma', 'mes', 'ton', 'ta', 'tes', 'son', 'sa', 'ses', 'notre', 'votre', 'eux', 'elles', 'nous', 'vous', 'mal', 'dos', 'poid', 'poids', 'maigr', 'muscl', 'stress', 'relax'];
        
        // Mots anglais courants
        $englishWords = ['i', 'you', 'he', 'she', 'it', 'we', 'they', 'the', 'is', 'are', 'am', 'was', 'were', 'be', 'have', 'has', 'do', 'does', 'did', 'will', 'would', 'can', 'could', 'should', 'may', 'might', 'must', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'from', 'by', 'about', 'what', 'where', 'when', 'how', 'why', 'tell', 'help', 'goal', 'looking', 'want', 'need'];
        
        $words = explode(' ', $message);
        $frenchCount = 0;
        $englishCount = 0;
        
        foreach ($words as $word) {
            // Nettoyer le mot
            $cleanWord = preg_replace('/[^a-z]/', '', $word);
            
            // Vérifier correspondance partielle
            foreach ($frenchWords as $fw) {
                if (strpos($cleanWord, $fw) === 0 && strlen($cleanWord) >= strlen($fw)) {
                    $frenchCount++;
                    break;
                }
            }
            
            foreach ($englishWords as $ew) {
                if (strpos($cleanWord, $ew) === 0 && strlen($cleanWord) >= strlen($ew)) {
                    $englishCount++;
                    break;
                }
            }
        }
        
        // Retourner la langue dominante
        return ($englishCount > $frenchCount) ? 'en' : 'fr';
    }

    // Analyser le message utilisateur et recommander des activités
    private function analyzeAndRecommend($userMessage, $activites) {
        $message = strtolower(trim($userMessage));
        
        // Analyser le message avec normalisation
        $normalizedMessage = $this->normalizeMessage($message);
        $words = array_filter(explode(' ', $normalizedMessage));
        if (count($words) < 2) {
            return ['error' => 'message_trop_court'];
        }

        // Tous les mots-clés pertinents (avec fuzzy matching) - EXPANDED
        $allKeywords = [
            'perte poids' => ['perte poids', 'perdre poids', 'perdre du poids', 'maigrir', 'maigre', 'regime', 'regimes', 'mincir', 'mince', 'calories', 'bruler calories', 'bruler graisses', 'gras', 'graisse', 'gros', 'surpoids'],
            'endurance' => ['endurance', 'courir', 'course', 'running', 'jogging', 'marathon', 'cardio', 'cardiaque', 'aerobic', 'souffle', 'respiration'],
            'musculation' => ['muscle', 'muscles', 'muscler', 'renforcer', 'force', 'forces', 'haltere', 'halteres', 'poids', 'tonifier', 'tonification', 'biceps', 'pectoraux', 'bras', 'jambes', 'jambe', 'cuisse', 'cuisses', 'abdos', 'abdominaux', 'travailler'],
            'flexibilite' => ['souplesse', 'flexible', 'flexibility', 'etirement', 'etirements', 'etirer', 'assouplir', 'mobilite', 'stretch', 'stretching', 'raideur', 'raide'],
            'relaxation' => ['stress', 'stresse', 'detente', 'detendu', 'relaxe', 'relaxer', 'calme', 'calmer', 'zen', 'anxiete', 'anxieux', 'anxieuse', 'nerveux', 'nerve', 'tension', 'respiration', 'meditation', 'mediter', 'respirer', 'paix', 'serenite'],
            'yoga' => ['yoga', 'yogi', 'meditation', 'mediter', 'stretching', 'pilates', 'asana', 'namaste', 'spirituel', 'spirituelle'],
            'equipe' => ['equipe', 'groupe', 'groupes', 'copains', 'copain', 'amis', 'ami', 'amie', 'ensemble', 'social', 'collectif', 'team', 'avec gens', 'avec monde', 'communaute', 'communal'],
            'eau' => ['eau', 'piscine', 'plage', 'mer', 'natation', 'nager', 'nageur', 'surf', 'surfer', 'plongee', 'plonger', 'aquatique', 'aqua', 'baignade', 'baignade', 'swimmer', 'swimming', 'mouille', 'mouille'],
            'combat' => ['combat', 'combattre', 'boxe', 'boxer', 'lutte', 'lutter', 'arts martiaux', 'karate', 'judo', 'taekwondo', 'kung fu', 'kick', 'punch', 'frapper'],
            'vitesse' => ['rapide', 'rapidement', 'vite', 'vitesse', 'explosif', 'explosion', 'sprint', 'sprinter', 'acceleration', 'accelerer', 'fast', 'speed', 'quick'],
            'sante' => ['sante', 'sain', 'bien etre', 'bien-etre', 'wellness', 'fitness', 'gym', 'gymnase', 'forme', 'en forme', 'immunitaire', 'immunite', 'healthy', 'health', 'wellnes'],
            'fun' => ['amusant', 'amuser', 'fun', 'plaisir', 'plaisant', 'jeux', 'jeu', 'jouer', 'divertissement', 'divertir', 'marrant', 'rigolo', 'cool', 'exciting', 'exciter', 'excitation'],
            'douleur' => ['douleur', 'douleurs', 'douloureux', 'mal', 'maux', 'dos', 'genou', 'genoux', 'articulation', 'articulations', 'recuperation', 'recuperer', 'blessure', 'blessure', 'pain', 'pains', 'inflammation', 'inflamme', 'inflammation', 'mal', 'ache', 'hurts', 'hurt', 'blesse'],
            'debutant' => ['debutant', 'debutants', 'facile', 'facilement', 'simple', 'simplement', 'commencer', 'commencement', 'novice', 'novices', 'apprenti', 'apprentis', 'first time', 'easy', 'easiest', 'beginner', 'beginners'],
            'avance' => ['avance', 'avancee', 'avances', 'difficile', 'difficilement', 'intense', 'intensite', 'intensif', 'expert', 'experts', 'professionnel', 'professionnelle', 'hardcore', 'hardcore', 'challenge', 'extrême', 'extreme', 'extremement', 'extreme', 'pro'],
            'energie' => ['energie', 'energique', 'dynamique', 'dynamisme', 'puissance', 'puissant', 'vivant', 'vitalite', 'vital', 'dynamique', 'boost', 'power', 'energize'],
            'exterieur' => ['exterieur', 'dehors', 'nature', 'plein air', 'plein-air', 'outdoor', 'montagne', 'montagnes', 'foret', 'forets', 'parc', 'parcs', 'route', 'routes', 'chemin', 'chemins', 'sentier', 'sentiers'],
            'interieur' => ['interieur', 'dedans', 'salle', 'salles', 'indoor', 'gym', 'gymnase', 'maison', 'maisons', 'interieur', 'dedans'],
            'competition' => ['competition', 'competitif', 'competitive', 'gagner', 'gain', 'gagnant', 'victorieux', 'victoire', 'champion', 'champions', 'championnat', 'competition', 'win', 'winning', 'compete'],
            'gratuit' => ['gratuit', 'gratuite', 'gratuitement', 'free', 'gratuitement', 'prix', 'couteux', 'cout', 'coute', 'payant', 'payer', 'cheap', 'inexpensif', 'free', 'pricey'],
        ];

        // Vérifier si le message contient au moins un mot-clé pertinent
        $messageHasKeyword = false;
        foreach ($allKeywords as $category => $keywordsList) {
            foreach ($keywordsList as $keyword) {
                if ($this->findSimilarKeyword($normalizedMessage, $keyword, 2)) {
                    $messageHasKeyword = true;
                    break 2;
                }
            }
        }

        // Si le message ne contient aucun mot-clé, retourner une erreur
        if (!$messageHasKeyword) {
            return ['error' => 'message_incompris'];
        }

        $recommendations = [];
        $scores = [];

        // Analyser chaque activité avec fuzzy matching
        foreach ($activites as $activite) {
            $score = 0;
            $name = strtolower($activite['nom']);
            $description = strtolower($activite['description'] ?? '');
            $categorie = strtolower($activite['categorie'] ?? '');
            $text = $name . ' ' . $description . ' ' . $categorie;

            // Vérifier les mots-clés avec fuzzy matching et pondération
            foreach ($allKeywords as $category => $keywordsList) {
                foreach ($keywordsList as $keyword) {
                    if ($this->findSimilarKeyword($normalizedMessage, $keyword, 2)) {
                        // Vérifier si le mot-clé est pertinent pour cette activité
                        if ($this->findSimilarKeyword($text, $keyword, 1)) {
                            $score += 12;
                        } else if ($this->isRelatedCategory($category, $categorie, $name)) {
                            $score += 8;
                        } else {
                            $score += 1;
                        }
                    }
                }
            }

            if ($score > 2) {
                $scores[$activite['id']] = [
                    'score' => $score,
                    'activite' => $activite
                ];
            }
        }

        // Si aucune correspondance trouvée
        if (empty($scores)) {
            return ['error' => 'message_incompris'];
        }

        // Trier par score décroissant
        usort($scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Retourner les 3 meilleures recommandations
        $result = [];
        foreach (array_slice($scores, 0, 3) as $item) {
            $result[] = $item['activite'];
        }

        return $result;
    }

    // Vérifier si une catégorie est liée à une activité
    private function isRelatedCategory($keyword, $categorie, $name) {
        $relations = [
            'perte poids' => ['cardio', 'course', 'vélo', 'natation', 'danse', 'boxe'],
            'endurance' => ['cardio', 'course', 'vélo', 'natation', 'trail', 'jogging'],
            'musculation' => ['fitness', 'musculation', 'gym', 'haltérophilie', 'crossfit'],
            'flexibilité' => ['yoga', 'pilates', 'stretching', 'danse'],
            'relaxation' => ['yoga', 'pilates', 'méditation', 'tai chi', 'marche'],
            'yoga' => ['yoga', 'pilates'],
            'équipe' => ['football', 'volley', 'basket', 'handball', 'rugby', 'hockey'],
            'eau' => ['natation', 'aquatique', 'surf', 'plongée', 'water polo'],
            'combat' => ['boxe', 'arts martiaux', 'karaté', 'judo', 'taekwondo'],
            'vitesse' => ['sprint', 'trail', 'cyclisme', 'badminton'],
            'santé' => ['fitness', 'gym', 'marche', 'running', 'yoga'],
            'fun' => ['football', 'volley', 'basket', 'badminton', 'tennis', 'danse'],
            'douleur' => ['yoga', 'pilates', 'aquatique', 'marche', 'tai chi'],
            'débutant' => ['marche', 'yoga', 'pilates', 'natation'],
            'avancé' => ['trail', 'cyclisme', 'boxe', 'arts martiaux', 'surf'],
        ];

        if (!isset($relations[$keyword])) {
            return false;
        }

        foreach ($relations[$keyword] as $related) {
            if (strpos($categorie . ' ' . $name, $related) !== false) {
                return true;
            }
        }

        return false;
    }

    // Générer une réponse personnalisée du chatbot
    private function generateBotResponse($recommendations) {
        if (empty($recommendations)) {
            return "Je n'ai pas trouvé d'activité correspondant exactement à votre description. N'hésitez pas à essayer une autre description ou à consulter toutes nos activités disponibles.";
        }

        $response = "Excellent ! Voici les activités sportives qui pourraient vous convenir :\n\n";
        
        foreach ($recommendations as $index => $activite) {
            $response .= ($index + 1) . ". <strong>" . htmlspecialchars($activite['nom']) . "</strong>";
            if (!empty($activite['description'])) {
                $response .= " - " . htmlspecialchars($activite['description']);
            }
            $response .= "\n";
        }

        return $response;
    }
}
