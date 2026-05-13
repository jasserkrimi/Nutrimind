/**
 * NutriMind — Notifications Sportives v4
 * - setTimeout PRÉCIS : déclenche exactement au bon moment
 * - Popup in-app TOUJOURS visible (sans permission navigateur)
 * - Détecte les nouvelles séances ajoutées et les programme automatiquement
 */
(function () {
  'use strict';

  var API_URL      = window.NM_API_URL || '/Nutrimind-main/api_planning.php';
  var ADVANCE_MIN  = 5;     // notification X minutes avant la séance
  var RELOAD_MS    = 60000; // recharger le planning toutes les 60s
  var scheduled    = {};    // clés déjà planifiées

  /* ─── POPUP IN-APP (fonctionne TOUJOURS, aucune permission nécessaire) ─── */
  function showPopup(seance, diffMin) {
    var old = document.getElementById('nm-popup');
    if (old) old.remove();

    if (!document.getElementById('nm-anim')) {
      var s = document.createElement('style');
      s.id = 'nm-anim';
      s.textContent = '@keyframes nmIn{from{opacity:0;transform:translateX(110%)}to{opacity:1;transform:translateX(0)}}@keyframes nmOut{to{opacity:0;transform:translateX(110%)}}';
      document.head.appendChild(s);
    }

    var label = diffMin > 0
      ? '⏰ Dans ' + diffMin + ' minute' + (diffMin > 1 ? 's' : '') + ' !'
      : '🚀 C\'est l\'heure !';

    var box = document.createElement('div');
    box.id  = 'nm-popup';
    box.style.cssText = 'position:fixed;top:80px;right:20px;z-index:2147483647;width:320px;max-width:calc(100vw - 32px);border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.4);animation:nmIn .5s cubic-bezier(.34,1.56,.64,1) both;font-family:Poppins,Arial,sans-serif';

    box.innerHTML =
      '<div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:18px 16px 14px;position:relative">' +
        '<button onclick="document.getElementById(\'nm-popup\').remove()" style="position:absolute;top:10px;right:10px;background:rgba(255,255,255,.2);border:none;color:#fff;width:24px;height:24px;border-radius:50%;cursor:pointer;font-size:13px;line-height:24px;text-align:center">✕</button>' +
        '<div style="font-size:2rem">🏋️</div>' +
        '<div style="color:#fff;font-weight:800;font-size:1rem;margin-top:6px">' + label + '</div>' +
        '<div style="color:rgba(255,255,255,.8);font-size:.78rem;margin-top:3px">Préparez votre équipement !</div>' +
      '</div>' +
      '<div style="background:#fff;padding:14px 16px">' +
        '<div style="font-weight:700;color:#1a1a2e;font-size:.95rem">' + esc(seance.activite) + '</div>' +
        '<div style="display:flex;gap:10px;color:#64748b;font-size:.78rem;margin-top:4px">' +
          '<span>🕐 ' + seance.heure + '</span>' +
          '<span>📅 ' + seance.jour + '</span>' +
          (seance.categorie ? '<span>🏷️ ' + esc(seance.categorie) + '</span>' : '') +
        '</div>' +
        '<div style="display:flex;gap:8px;margin-top:12px">' +
          '<button onclick="document.getElementById(\'nm-popup\').remove()" style="flex:1;padding:9px;background:#f1f5f9;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:.8rem;color:#475569">Ignorer</button>' +
          '<button onclick="document.getElementById(\'nm-popup\').remove()" style="flex:2;padding:9px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;border-radius:8px;cursor:pointer;font-weight:700;font-size:.8rem;color:#fff">✅ J\'y vais !</button>' +
        '</div>' +
      '</div>';

    document.body.appendChild(box);

    // Fermeture auto après 30s
    setTimeout(function () {
      var p = document.getElementById('nm-popup');
      if (p) { p.style.animation = 'nmOut .3s ease forwards'; setTimeout(function(){ if(document.getElementById('nm-popup')) p.remove(); }, 300); }
    }, 30000);

    // Son bip
    try {
      var ac = new (window.AudioContext || window.webkitAudioContext)();
      [0, 0.18].forEach(function(t){ var o=ac.createOscillator(),g=ac.createGain(); o.connect(g); g.connect(ac.destination); o.frequency.value=660; g.gain.setValueAtTime(0.25,ac.currentTime+t); g.gain.exponentialRampToValueAtTime(.001,ac.currentTime+t+.35); o.start(ac.currentTime+t); o.stop(ac.currentTime+t+.35); });
    } catch(e) {}
  }

  /* ─── NOTIFICATION NAVIGATEUR (bonus si permission) ─── */
  function browserNotif(seance, diffMin) {
    if (typeof Notification === 'undefined' || Notification.permission !== 'granted') return;
    try {
      var n = new Notification('🏋️ ' + seance.activite + ' — dans ' + diffMin + ' min', {
        body: 'Heure : ' + seance.heure + '\nJour : ' + seance.jour,
        icon: (window.NM_BASE_URL || '/Nutrimind-main/') + 'views/assets_front/img/logooo.png',
        tag: 'nm-' + seance.activite + seance.heure,
      });
      n.onclick = function(){ window.focus(); n.close(); };
      setTimeout(function(){ n.close(); }, 10000);
    } catch(e) {}
  }

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;'); }

  /* ─── PLANIFIER UNE SÉANCE AVEC setTimeout PRÉCIS ─── */
  function scheduleSeance(seance) {
    var key = seance.activite + '_' + seance.heure + '_' + seance.jour;
    if (scheduled[key]) return; // déjà planifiée

    var parts = (seance.heure || '').split(':');
    if (parts.length < 2) return;

    var now  = new Date();
    var nowMs = now.getTime();

    // Calculer l'heure de la séance en ms aujourd'hui
    var seanceMs = new Date(
      now.getFullYear(), now.getMonth(), now.getDate(),
      parseInt(parts[0], 10), parseInt(parts[1], 10), 0
    ).getTime();

    // Moment où déclencher = séance - ADVANCE_MIN
    var triggerMs = seanceMs - (ADVANCE_MIN * 60000);
    var delay = triggerMs - nowMs;

    if (delay < -ADVANCE_MIN * 60000) {
      // Séance complètement passée, ignorer
      scheduled[key] = 'past';
      return;
    }

    scheduled[key] = 'planned';

    if (delay <= 0) {
      // Séance est dans la fenêtre ou vient de passer — déclencher maintenant
      var diffMin = Math.max(0, Math.round((seanceMs - nowMs) / 60000));
      showPopup(seance, diffMin);
      browserNotif(seance, diffMin);
      console.log('[NutriMind] 🔔 Déclenché MAINTENANT :', seance.activite, '@', seance.heure);
    } else {
      // Planifier pour dans `delay` millisecondes
      var minutesAvant = Math.round(delay / 60000);
      console.log('[NutriMind] ⏱️ Planifié :', seance.activite, '@', seance.heure, '→ dans', minutesAvant, 'min (setTimeout', Math.round(delay/1000)+'s)');
      setTimeout(function () {
        var diffMin2 = Math.max(0, Math.round((seanceMs - Date.now()) / 60000));
        showPopup(seance, diffMin2);
        browserNotif(seance, diffMin2);
        console.log('[NutriMind] 🔔 setTimeout déclenché :', seance.activite, '@', seance.heure);
      }, delay);
    }
  }

  /* ─── WIDGET PROCHAINE SÉANCE ─── */
  function injectWidget() {
    if (document.getElementById('nm-widget')) return;
    var w = document.createElement('div');
    w.id = 'nm-widget';
    w.style.cssText = 'display:none;position:fixed;bottom:24px;left:20px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;padding:9px 18px;border-radius:50px;font-size:.78rem;font-family:Poppins,sans-serif;z-index:9995;box-shadow:0 4px 16px rgba(0,0,0,.35);white-space:nowrap;cursor:default;transition:opacity .5s';
    document.body.appendChild(w);
  }

  function updateWidget(seances) {
    var w = document.getElementById('nm-widget');
    if (!w) return;
    var now   = new Date();
    var nowMs = now.getTime();
    var next  = null, minDelay = Infinity;

    seances.forEach(function(s){
      var p = (s.heure||'').split(':');
      if (p.length < 2) return;
      var sMs = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(p[0],10), parseInt(p[1],10), 0).getTime();
      var d = sMs - nowMs;
      if (d > 0 && d < minDelay) { minDelay = d; next = s; }
    });

    if (next) {
      var minLeft = Math.round(minDelay / 60000);
      w.innerHTML = '🏋️&nbsp;&nbsp;' + esc(next.activite) + ' &nbsp;<strong>dans ' + minLeft + ' min</strong>&nbsp; (' + next.heure + ')';
      w.style.display  = 'block';
      w.style.opacity  = '1';
    } else {
      // Aucune séance à venir → disparaître en fondu
      w.style.opacity = '0';
      setTimeout(function(){ w.style.display = 'none'; }, 500);
    }
  }

  /* ─── CHARGER LE PLANNING ET PLANIFIER TOUTES LES SÉANCES ─── */
  function loadAndSchedule() {
    fetch(API_URL + '?_=' + Date.now())
      .then(function(r){ return r.json(); })
      .then(function(d){
        if (!d.success || !Array.isArray(d.seances)) return;
        console.log('[NutriMind] Planning chargé :', d.seances.length, 'séance(s) ce', d.jour);
        d.seances.forEach(scheduleSeance);
        updateWidget(d.seances);
      })
      .catch(function(e){ console.warn('[NutriMind] API error:', e.message); });
  }

  /* ─── DEMANDE PERMISSION NAVIGATEUR (optionnel) ─── */
  function askPermission() {
    if (typeof Notification === 'undefined' || Notification.permission !== 'default') return;
    if (Date.now() - parseInt(localStorage.getItem('nm_perm_dis')||'0') < 86400000) return;
    var bar = document.createElement('div');
    bar.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:11px 16px;border-radius:14px;font-size:.8rem;font-family:Poppins,sans-serif;z-index:9994;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.4);max-width:90vw';
    bar.innerHTML = '<span>🔔 Activer aussi les notifications navigateur ?</span>' +
      '<button id="nm-py" style="background:#6366f1;border:none;color:#fff;padding:5px 11px;border-radius:7px;cursor:pointer;font-weight:700;font-size:.78rem">Activer</button>' +
      '<button id="nm-pn" style="background:rgba(255,255,255,.15);border:none;color:#fff;padding:5px 9px;border-radius:7px;cursor:pointer;font-size:.78rem">✕</button>';
    document.body.appendChild(bar);
    document.getElementById('nm-py').onclick = function(){ Notification.requestPermission().then(function(){ bar.remove(); }); };
    document.getElementById('nm-pn').onclick = function(){ localStorage.setItem('nm_perm_dis', Date.now()); bar.remove(); };
  }

  /* ─── INIT ─── */
  function init() {
    injectWidget();
    loadAndSchedule();
    // Recharger périodiquement pour détecter les nouvelles séances ajoutées
    setInterval(loadAndSchedule, RELOAD_MS);
    setTimeout(askPermission, 3000);
    // Mettre à jour le widget toutes les 30s
    setInterval(function(){ loadAndSchedule(); }, 30000);
    console.log('[NutriMind] 🔔 Notifications v4 actives | setTimeout précis | API:', API_URL);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
