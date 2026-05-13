/**
 * NutriMind Sport AI - Moteur de recommandation sportive intelligent
 * Version 2.0 — Sans API externe, 100% local
 */

window.SportAI = (function () {

  /* ========================================================
     BASE DE CONNAISSANCES — Activités & Exercices
  ======================================================== */
  const KB = {
    activites: [
      {
        nom: "Yoga",
        emoji: "🧘",
        tags: ["stress", "anxiété", "tension", "dos", "flexibilité", "calme", "respiration", "sommeil", "fatigue", "mental"],
        description: "Parfait pour réduire le stress et améliorer la flexibilité du corps.",
        exercices: [
          { nom: "Salutation au Soleil", duree: "10 min", reps: "5 cycles", difficulte: "Débutant" },
          { nom: "Posture de l'Enfant (Balasana)", duree: "5 min", reps: "maintien", difficulte: "Débutant" },
          { nom: "Posture du Cobra (Bhujangasana)", duree: "8 min", reps: "8 reps", difficulte: "Intermédiaire" },
          { nom: "Posture du Guerrier (Virabhadrasana)", duree: "10 min", reps: "3×10", difficulte: "Intermédiaire" }
        ],
        conseil: "Pratiquez le matin à jeun pour de meilleurs résultats. Commencez par 20 min/jour."
      },
      {
        nom: "Course à pied",
        emoji: "🏃",
        tags: ["poids", "mincir", "cardio", "endurance", "calories", "gras", "ventre", "obésité", "forme", "energie"],
        description: "Excellente activité cardio pour brûler des calories et améliorer l'endurance.",
        exercices: [
          { nom: "Footing léger", duree: "20 min", reps: "continu", difficulte: "Débutant" },
          { nom: "Intervalles (HIIT)", duree: "25 min", reps: "8×(1 min rapide / 1 min marche)", difficulte: "Avancé" },
          { nom: "Côtes (montées)", duree: "15 min", reps: "6 montées", difficulte: "Intermédiaire" },
          { nom: "Course longue distance", duree: "45 min", reps: "continu", difficulte: "Avancé" }
        ],
        conseil: "Portez de bonnes chaussures. Augmentez progressivement la distance (+10% par semaine)."
      },
      {
        nom: "Musculation",
        emoji: "💪",
        tags: ["muscle", "force", "prise de masse", "tonifier", "bras", "jambes", "dos", "poitrine", "corps", "maigre"],
        description: "Idéal pour renforcer les muscles et sculter le corps.",
        exercices: [
          { nom: "Pompes", duree: "10 min", reps: "4×15", difficulte: "Débutant" },
          { nom: "Squat", duree: "12 min", reps: "4×12", difficulte: "Débutant" },
          { nom: "Développé couché", duree: "15 min", reps: "4×10", difficulte: "Intermédiaire" },
          { nom: "Tractions (Pull-ups)", duree: "10 min", reps: "3×8", difficulte: "Avancé" },
          { nom: "Soulevé de terre", duree: "15 min", reps: "4×8", difficulte: "Avancé" }
        ],
        conseil: "Reposez 48h entre les séances du même groupe musculaire. Pensez à la nutrition protéinée."
      },
      {
        nom: "Natation",
        emoji: "🏊",
        tags: ["articulation", "genou", "hanche", "dos", "blessure", "rééducation", "surpoids", "douleur", "senior", "eau"],
        description: "Sport complet et doux, idéal si vous avez des problèmes articulaires.",
        exercices: [
          { nom: "Crawl lent", duree: "20 min", reps: "500m", difficulte: "Débutant" },
          { nom: "Brasse", duree: "20 min", reps: "400m", difficulte: "Débutant" },
          { nom: "Dos crawlé", duree: "15 min", reps: "300m", difficulte: "Intermédiaire" },
          { nom: "Intervals nage rapide", duree: "25 min", reps: "10×50m", difficulte: "Avancé" }
        ],
        conseil: "La natation sollicite 90% des muscles du corps sans impact. 3 séances/semaine suffisent."
      },
      {
        nom: "Cyclisme",
        emoji: "🚴",
        tags: ["jambes", "endurance", "cardio", "genou", "outdoor", "plein air", "nature", "poids", "dehors"],
        description: "Sport d'endurance excellent pour les jambes et le cardio.",
        exercices: [
          { nom: "Sortie vélo débutant", duree: "30 min", reps: "15km", difficulte: "Débutant" },
          { nom: "Côtes & relances", duree: "45 min", reps: "6 côtes", difficulte: "Intermédiaire" },
          { nom: "Sortie longue distance", duree: "90 min", reps: "40km", difficulte: "Avancé" }
        ],
        conseil: "Réglez bien la hauteur de la selle pour protéger vos genoux."
      },
      {
        nom: "Pilates",
        emoji: "🤸",
        tags: ["posture", "dos", "core", "abdominaux", "gainage", "femme", "grossesse", "équilibre", "douleur", "lombaire"],
        description: "Renforcement du centre du corps (core) et amélioration de la posture.",
        exercices: [
          { nom: "The Hundred", duree: "5 min", reps: "100 pompes de bras", difficulte: "Intermédiaire" },
          { nom: "Roll Up", duree: "8 min", reps: "10 reps", difficulte: "Intermédiaire" },
          { nom: "Planche latérale", duree: "6 min", reps: "3×30 sec", difficulte: "Intermédiaire" },
          { nom: "Bridge (Pont)", duree: "8 min", reps: "3×15", difficulte: "Débutant" }
        ],
        conseil: "Concentrez-vous sur la qualité du mouvement, pas la quantité."
      },
      {
        nom: "HIIT",
        emoji: "⚡",
        tags: ["rapide", "efficace", "temps", "occupé", "brûler", "calories", "intensité", "sport maison", "30 minutes", "résultats"],
        description: "Entraînement intense par intervalles — maximum de résultats en minimum de temps.",
        exercices: [
          { nom: "Burpees", duree: "3 min", reps: "4×20 sec / 10 sec repos", difficulte: "Avancé" },
          { nom: "Mountain Climbers", duree: "3 min", reps: "4×30 sec", difficulte: "Intermédiaire" },
          { nom: "Jump Squats", duree: "3 min", reps: "4×15", difficulte: "Intermédiaire" },
          { nom: "High Knees", duree: "3 min", reps: "4×30 sec", difficulte: "Débutant" }
        ],
        conseil: "Ne faites pas de HIIT plus de 3-4 fois par semaine. Récupérez bien entre les séances."
      },
      {
        nom: "Arts Martiaux",
        emoji: "🥋",
        tags: ["défense", "confiance", "discipline", "jeune", "enfant", "adolescent", "concentration", "coordination", "agressivité"],
        description: "Développe la discipline, la confiance en soi et la coordination.",
        exercices: [
          { nom: "Shadow Boxing", duree: "10 min", reps: "3 rounds × 3 min", difficulte: "Débutant" },
          { nom: "Katas de base", duree: "20 min", reps: "5 répétitions", difficulte: "Débutant" },
          { nom: "Sac de frappe", duree: "15 min", reps: "5 rounds × 2 min", difficulte: "Intermédiaire" }
        ],
        conseil: "Trouvez un club local avec un bon coach. La technique prime sur la force."
      }
    ],

    salutations: ["bonjour", "salut", "bonsoir", "hey", "coucou", "hello", "hi", "salam", "ahlan"],
    questionsBien: ["ça va", "comment tu vas", "comment vas-tu", "tu vas bien", "comment allez-vous"],
    questionsCapacite: ["que peux-tu faire", "tu fais quoi", "c'est quoi ton rôle", "comment tu m'aides", "aide", "help", "quoi faire"],
    questionsMerci: ["merci", "thanks", "thank you", "bravo", "super", "parfait", "excellent"]
  };

  /* ========================================================
     MOTEUR DE DÉTECTION
  ======================================================== */
  function normalize(text) {
    return text.toLowerCase()
      .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9\s]/g, " ")
      .trim();
  }

  function matchKeywords(message, keywords) {
    var norm = normalize(message);
    return keywords.some(function(k) {
      var nk = normalize(k);
      return nk.length > 0 && norm.includes(nk);
    });
  }

  function findBestActivity(message) {
    const norm = normalize(message);
    let bestMatch = null, bestScore = 0;
    KB.activites.forEach(act => {
      let score = 0;
      act.tags.forEach(tag => { if (norm.includes(normalize(tag))) score++; });
      if (score > bestScore) { bestScore = score; bestMatch = act; }
    });
    return bestScore >= 1 ? bestMatch : null;
  }

  function findMultipleActivities(message, limit = 3) {
    const norm = normalize(message);
    let results = [];
    KB.activites.forEach(act => {
      let score = 0;
      act.tags.forEach(tag => { if (norm.includes(normalize(tag))) score++; });
      if (score > 0) results.push({ ...act, score });
    });
    results.sort((a, b) => b.score - a.score);
    return results.slice(0, limit);
  }

  /* ========================================================
     GÉNÉRATEUR DE RÉPONSES HTML
  ======================================================== */
  function buildActivityCard(act) {
    const diffColors = { "Débutant": "#22c55e", "Intermédiaire": "#f59e0b", "Avancé": "#ef4444" };
    const exercicesHTML = act.exercices.map(ex => `
      <div class="nm-exercise-item">
        <div class="nm-exercise-header">
          <span class="nm-exercise-name">${ex.nom}</span>
          <span class="nm-exercise-diff" style="background:${diffColors[ex.difficulte] || '#888'}">${ex.difficulte}</span>
        </div>
        <div class="nm-exercise-meta">
          <span>⏱ ${ex.duree}</span>
          <span>🔄 ${ex.reps}</span>
        </div>
      </div>
    `).join('');

    return `
      <div class="nm-activity-card">
        <div class="nm-activity-header">
          <span class="nm-activity-emoji">${act.emoji}</span>
          <div>
            <div class="nm-activity-name">${act.nom}</div>
            <div class="nm-activity-desc">${act.description}</div>
          </div>
        </div>
        <div class="nm-exercises-section">
          <div class="nm-section-title">📋 Exercices recommandés</div>
          ${exercicesHTML}
        </div>
        <div class="nm-conseil-box"><span>💡</span> ${act.conseil}</div>
      </div>
    `;
  }

  function buildMultiCard(activities) {
    return activities.map((act, i) => `
      <div class="nm-multi-card" style="animation-delay:${i * 0.1}s">
        <div class="nm-multi-header">
          <span>${act.emoji}</span>
          <div><strong>${act.nom}</strong><p>${act.description}</p></div>
        </div>
        <div class="nm-multi-tags">
          ${act.exercices.slice(0, 3).map(e => `<span>${e.nom}</span>`).join('')}
        </div>
      </div>
    `).join('');
  }

  /* ========================================================
     PROCESSEUR PRINCIPAL
  ======================================================== */
  function getResponse(userMessage) {
    const msg = userMessage.trim();

    if (matchKeywords(msg, KB.salutations)) {
      return { type: "text", text: `👋 <strong>Bonjour !</strong> Je suis <strong>NutriBot</strong>, votre coach sportif IA ! 🏋️<br><br>Dites-moi votre <strong>problème</strong> ou votre <strong>objectif</strong> et je vous recommanderai l'activité et les exercices parfaits.<br><br><em>Exemple : "j'ai mal au dos", "je veux perdre du poids", "je suis stressé"...</em>` };
    }
    if (matchKeywords(msg, KB.questionsBien)) {
      return { type: "text", text: `😊 Je vais très bien, merci ! Quel est votre <strong>problème</strong> ou votre <strong>objectif sportif</strong> aujourd'hui ?` };
    }
    if (matchKeywords(msg, KB.questionsCapacite)) {
      return { type: "text", text: `🤖 <strong>Je peux vous aider à :</strong><br><br>✅ Trouver l'<strong>activité sportive</strong> adaptée à vos problèmes<br>✅ Recommander des <strong>exercices spécifiques</strong> avec durées et répétitions<br>✅ Donner des <strong>conseils personnalisés</strong><br><br><strong>Parlez-moi de votre situation :</strong><br>• Douleurs (dos, genou, épaule...)<br>• Objectifs (perdre du poids, prendre du muscle...)<br>• État mental (stress, fatigue, anxiété...)<br>• Contraintes (peu de temps, pas de salle...)` };
    }
    if (matchKeywords(msg, KB.questionsMerci)) {
      return { type: "text", text: `😊 Avec plaisir ! Bonne séance et n'hésitez pas à revenir si vous avez d'autres questions. 💪🏃` };
    }

    const multi = findMultipleActivities(msg);
    if (multi.length >= 2) {
      const best = multi[0];
      return {
        type: "mixed",
        text: `🎯 J'ai trouvé <strong>${multi.length} activités</strong> adaptées !<br>Ma <strong>recommandation principale</strong> : <strong>${best.emoji} ${best.nom}</strong>`,
        card: buildActivityCard(best),
        extra: `<br><div class="nm-other-title">📌 Autres options :</div>${buildMultiCard(multi.slice(1))}`
      };
    }

    const act = findBestActivity(msg);
    if (act) {
      return { type: "card", text: `🎯 Basé sur votre situation, je vous recommande :<br><strong>${act.emoji} ${act.nom}</strong>`, card: buildActivityCard(act) };
    }

    return {
      type: "suggestion",
      text: `🤔 Je n'ai pas bien compris. Pouvez-vous préciser votre problème ou objectif ?<br><br><strong>Exemples de questions :</strong>`,
      suggestions: ["J'ai mal au dos 🦴", "Je veux perdre du poids ⚖️", "Je suis stressé 😣", "Je veux des muscles 💪", "J'ai peu de temps ⏱️", "Problème de genou 🦵"]
    };
  }

  return { getResponse, getActivities: () => KB.activites, normalize };

})();
