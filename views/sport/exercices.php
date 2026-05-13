<?php ob_start();

function getVid($nom) {
  $n = mb_strtolower($nom);
  $map = [
    'burpee'=>'dZgVxmf6jkA','mountain'=>'nmwgirgXLYM','jump squat'=>'A-cFYWvaHr0',
    'high knee'=>'tx5LvBdBCPc','box jump'=>'NBY9-kTuHEk','jumping jack'=>'iSSAk4XCsRA',
    'pompe'=>'IODxDxX7oi4','push'=>'IODxDxX7oi4','traction'=>'eGo4IYlbE5g',
    'pull-up'=>'eGo4IYlbE5g','bench'=>'rT7DgCr-3pg','squat'=>'aclHkVaku9U',
    'curl'=>'ykJmrZ5v0Oo','dip'=>'2z8JmcrK-As','salutation'=>'v7AYKMP6rOE',
    'guerrier'=>'VpWMgLFd5qE','chien'=>'EC7RGZoUoiY','cobra'=>'2RCFbIrGmrw',
    'arbre'=>'wdln9qWYloU','footing'=>'flp62R2v3vs','crawl'=>'jkPGH_Z0Zio',
    'brasse'=>'am8a9rNM0Es','papillon'=>'BxjPnMWPaKI','soulevé'=>'op9kVnSso6Q',
    'deadlift'=>'op9kVnSso6Q','militaire'=>'CnBmiBqp-AI','overhead'=>'CnBmiBqp-AI',
    'cent'=>'BoKvIiGEBOY','roulade'=>'vz6kVY2p50g','pont'=>'1jFBBBbMsD4',
    'planche'=>'pSHjTRCQxIw','gainage'=>'pSHjTRCQxIw','fente'=>'QOVaHwm-Q6U',
    'footing'=>'flp62R2v3vs','intervalle'=>'YAp_vT1eXrI','vélo'=>'sGS-vaNR3Yk',
  ];
  foreach ($map as $k=>$v) { if (mb_strpos($n,$k)!==false) return $v; }
  return 'oAPCPjnU1wA';
}

function getImg($nom) {
  $n = mb_strtolower($nom);
  $imgs = [
    'burpee'=>'photo-1601422407692-ec4eeec1d9b3','squat'=>'photo-1574680096145-d05b474e2155',
    'traction'=>'photo-1598971861713-54ad16a7e72e','pompe'=>'photo-1571019613454-1cb2f99b2d8b',
    'push'=>'photo-1571019613454-1cb2f99b2d8b','soulevé'=>'photo-1583454110551-21f2fa2afe61',
    'bench'=>'photo-1583454110551-21f2fa2afe61','curl'=>'photo-1534438327276-14e5300c3a48',
    'yoga'=>'photo-1506126613408-eca07ce68773','salutation'=>'photo-1506126613408-eca07ce68773',
    'natation'=>'photo-1530549387789-4c1017266635','crawl'=>'photo-1530549387789-4c1017266635',
    'brasse'=>'photo-1530549387789-4c1017266635','course'=>'photo-1571008887538-b36bb32f4571',
    'footing'=>'photo-1571008887538-b36bb32f4571','vélo'=>'photo-1534787238916-9ba6764efd4f',
    'planche'=>'photo-1566241142559-40e1dab266c6','mountain'=>'photo-1601422407692-ec4eeec1d9b3',
    'pilates'=>'photo-1544367567-0f2fcb009e0b','pont'=>'photo-1544367567-0f2fcb009e0b',
  ];
  foreach ($imgs as $k=>$v) { if (mb_strpos($n,$k)!==false) return 'https://images.unsplash.com/'.$v.'?w=500&q=80'; }
  return 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=500&q=80';
}

function getDiffColor($d) {
  if($d==='Débutant') return '#22c55e';
  if($d==='Avancé') return '#ef4444';
  return '#f59e0b';
}
?>
<style>
body{background:#0a0f1e}
.expage{padding:0 0 60px;font-family:Poppins,sans-serif}
.ex-hero{background:linear-gradient(135deg,#1e1b4b,#312e81,#1e40af);border-radius:20px;padding:40px 30px;margin-bottom:36px;text-align:center;color:#fff;position:relative;overflow:hidden}
.ex-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 50%,rgba(99,102,241,.25),transparent 70%);pointer-events:none}
.ex-hero h1{font-size:2.2rem;font-weight:800;margin-bottom:8px;position:relative}
.ex-hero p{opacity:.8;position:relative;margin-bottom:20px}
.ex-stats{display:flex;justify-content:center;gap:16px;flex-wrap:wrap;position:relative}
.ex-stat{background:rgba(255,255,255,.12);border-radius:12px;padding:10px 22px;backdrop-filter:blur(8px)}
.ex-stat strong{display:block;font-size:1.5rem;font-weight:800}
.ex-stat span{font-size:.75rem;opacity:.75}
.ex-back{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.07);color:#94a3b8;border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:10px 20px;text-decoration:none;font-size:.88rem;margin-bottom:28px;transition:all .2s}
.ex-back:hover{background:rgba(99,102,241,.25);color:#fff}
.ex-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:22px}
.ex-card{background:#1e293b;border-radius:18px;overflow:hidden;border:1px solid rgba(255,255,255,.07);cursor:pointer;transition:all .3s cubic-bezier(.34,1.56,.64,1)}
.ex-card:hover{transform:translateY(-8px) scale(1.02);box-shadow:0 24px 50px rgba(0,0,0,.45)}
.ex-card-img{height:190px;position:relative;overflow:hidden}
.ex-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.ex-card:hover .ex-card-img img{transform:scale(1.08)}
.ex-card-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 45%,rgba(15,23,42,.92))}
.ex-diff-badge{position:absolute;top:12px;left:12px;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;color:#fff}
.ex-card-body{padding:18px}
.ex-card-name{font-size:1rem;font-weight:700;color:#fff;margin-bottom:6px}
.ex-card-desc{font-size:.8rem;color:#94a3b8;line-height:1.5;margin-bottom:14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ex-cta{width:100%;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);color:#a5b4fc;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s}
.ex-cta:hover{background:#6366f1;border-color:#6366f1;color:#fff}
.ex-overlay{position:fixed;inset:0;background:rgba(0,0,0,.88);backdrop-filter:blur(10px);z-index:99999;display:none;align-items:center;justify-content:center;padding:16px}
.ex-overlay.show{display:flex}
.ex-modal{background:#0f172a;border-radius:22px;width:100%;max-width:880px;max-height:90vh;overflow-y:auto;border:1px solid rgba(99,102,241,.35);box-shadow:0 40px 80px rgba(0,0,0,.7);animation:mIn .35s cubic-bezier(.34,1.56,.64,1)}
@keyframes mIn{from{opacity:0;transform:scale(.85) translateY(30px)}to{opacity:1;transform:scale(1) translateY(0)}}
.m-head{padding:20px 24px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between}
.m-title{font-size:1.25rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px}
.m-close{width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s}
.m-close:hover{background:rgba(239,68,68,.35);transform:rotate(90deg)}
.m-body{padding:24px}
.m-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:22px}
@media(max-width:600px){.m-grid{grid-template-columns:1fr}}
.m-video{border-radius:14px;overflow:hidden;aspect-ratio:16/9;background:#1e293b}
.m-video iframe{width:100%;height:100%;border:none}
.m-info{display:flex;flex-direction:column;gap:12px}
.m-block{background:rgba(255,255,255,.04);border-radius:12px;padding:14px;border:1px solid rgba(255,255,255,.06)}
.m-block h4{font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:6px;font-weight:700}
.m-block p{font-size:.85rem;color:#e2e8f0;line-height:1.6;margin:0}
.timer-wrap{background:linear-gradient(135deg,#1e293b,#0f172a);border-radius:18px;padding:22px;border:1px solid rgba(99,102,241,.3)}
.timer-title{text-align:center;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:#a5b4fc;margin-bottom:14px}
.timer-presets{display:flex;flex-wrap:wrap;gap:7px;justify-content:center;margin-bottom:16px}
.t-pre{background:rgba(99,102,241,.18);border:1px solid rgba(99,102,241,.3);color:#a5b4fc;border-radius:20px;padding:5px 14px;font-size:.78rem;cursor:pointer;transition:all .2s}
.t-pre:hover,.t-pre.active{background:#6366f1;color:#fff;border-color:#6366f1}
.timer-display{text-align:center;margin-bottom:16px}
.t-digits{font-size:3.8rem;font-weight:900;color:#fff;font-family:'Courier New',monospace;letter-spacing:4px;text-shadow:0 0 30px rgba(99,102,241,.6)}
.t-status{font-size:.75rem;color:#64748b;text-transform:uppercase;letter-spacing:2px;margin-top:4px}
.t-bar-wrap{height:6px;background:rgba(255,255,255,.08);border-radius:3px;margin-top:10px;overflow:hidden}
.t-bar{height:100%;background:linear-gradient(90deg,#6366f1,#8b5cf6);border-radius:3px;transition:width .5s linear;width:0}
.timer-btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.t-btn{padding:10px 22px;border-radius:12px;border:none;font-size:.85rem;font-weight:700;cursor:pointer;transition:all .2s}
.t-start{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff}
.t-pause{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;display:none}
.t-reset{background:rgba(255,255,255,.1);color:#94a3b8}
.t-done{display:none;text-align:center;margin-top:14px;padding:14px;background:rgba(34,197,94,.13);border-radius:12px;border:1px solid rgba(34,197,94,.3);color:#4ade80;font-weight:700}
</style>

<div class="expage">
  <a href="index.php?c=home" class="ex-back">← Retour aux activités</a>
  <div class="ex-hero">
    <h1>🏋️ <?= htmlspecialchars($activite['nom']) ?></h1>
    <p><?= htmlspecialchars($activite['description'] ?? 'Découvrez et pratiquez les exercices') ?></p>
    <div class="ex-stats">
      <div class="ex-stat"><strong><?= count($exercices) ?></strong><span>Exercices</span></div>
      <div class="ex-stat"><strong><?= count(array_filter($exercices,fn($e)=>($e['difficulte']??'')==='Débutant')) ?></strong><span>Débutant</span></div>
      <div class="ex-stat"><strong><?= count(array_filter($exercices,fn($e)=>($e['difficulte']??'')==='Avancé')) ?></strong><span>Avancé</span></div>
    </div>
  </div>
  <?php if(empty($exercices)): ?>
  <div style="text-align:center;padding:60px;color:#64748b">
    <div style="font-size:4rem">🏋️</div>
    <p>Aucun exercice. <a href="/nutrimind_int/seed_exercices_sport.php" style="color:#6366f1">Ajouter des exercices</a></p>
  </div>
  <?php else: ?>
  <div class="ex-grid" id="exGrid"></div>
  <?php endif; ?>
</div>

<div class="ex-overlay" id="exOverlay">
  <div class="ex-modal">
    <div class="m-head">
      <div class="m-title"><span id="mEmoji"></span><span id="mTitle"></span></div>
      <button class="m-close" onclick="closeEx()">✕</button>
    </div>
    <div class="m-body">
      <div class="m-grid">
        <div class="m-video" id="mVideoWrap">
          <div id="mThumbWrap" style="position:relative;width:100%;height:100%;cursor:pointer" onclick="loadVideo()">
            <img id="mThumb" src="" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">
            <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(0,0,0,.45);border-radius:14px">
              <div style="width:64px;height:64px;background:rgba(255,0,0,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:10px">
                <svg viewBox='0 0 24 24' fill='white' width='30' height='30'><path d='M8 5v14l11-7z'/></svg>
              </div>
              <span style="color:white;font-size:.85rem;font-weight:600">▶ Cliquez pour voir la démonstration</span>
            </div>
          </div>
          <div id="mFallback" style="display:none;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:20px;text-align:center">
            <div style="font-size:3rem;margin-bottom:12px">🎬</div>
            <p style="color:#94a3b8;font-size:.9rem;margin-bottom:16px">Vidéo non disponible directement</p>
            <a id="mYtLink" href="#" target="_blank" style="background:#ff0000;color:white;padding:10px 20px;border-radius:10px;text-decoration:none;font-weight:700">🔍 Voir sur YouTube</a>
          </div>
          <iframe id="mIframe" src="" allowfullscreen style="display:none;width:100%;height:100%;border:none;border-radius:14px"></iframe>
        </div>
        <div class="m-info">
          <div class="m-block"><h4>📋 Description</h4><p id="mDesc"></p></div>
          <div class="m-block"><h4>💪 Difficulté</h4><p id="mDiff" style="font-weight:700"></p></div>
        </div>
      </div>
      <div class="timer-wrap">
        <div class="timer-title">⏱ Chronomètre</div>
        <div class="timer-presets">
          <button class="t-pre" onclick="setT(20,this)">20s</button>
          <button class="t-pre" onclick="setT(30,this)">30s</button>
          <button class="t-pre" onclick="setT(45,this)">45s</button>
          <button class="t-pre" onclick="setT(60,this)">1 min</button>
          <button class="t-pre" onclick="setT(90,this)">1:30</button>
          <button class="t-pre" onclick="setT(120,this)">2 min</button>
          <button class="t-pre" onclick="setT(180,this)">3 min</button>
          <button class="t-pre" onclick="setT(300,this)">5 min</button>
        </div>
        <div class="timer-display">
          <div class="t-digits" id="tDisp">00:00</div>
          <div class="t-status" id="tStatus">Sélectionnez une durée</div>
          <div class="t-bar-wrap"><div class="t-bar" id="tBar"></div></div>
        </div>
        <div class="timer-btns">
          <button class="t-btn t-start" id="tBtnStart" onclick="startT()">▶ Démarrer</button>
          <button class="t-btn t-pause" id="tBtnPause" onclick="pauseT()">⏸ Pause</button>
          <button class="t-btn t-reset" onclick="resetT()">↺ Reset</button>
        </div>
        <div class="t-done" id="tDone">🎉 Exercice terminé ! Excellent travail !</div>
      </div>
    </div>
  </div>
</div>

<?php
$exData = [];
foreach ($exercices as $ex) {
  $vid = $ex['url_video'] ?? '';
  if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $vid, $m)) {
    $vid = $m[1];
  } elseif (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $vid)) {
    $vid = getVid($ex['nom']);
  }
  $exData[] = ['nom'=>$ex['nom'],'desc'=>$ex['description']??'Un exercice efficace.','vid'=>$vid,'diff'=>$ex['difficulte']??'N/A','img'=>getImg($ex['nom']),'color'=>getDiffColor($ex['difficulte']??'')];
}
?>
<script>
var EX=<?= json_encode($exData,JSON_HEX_QUOT|JSON_HEX_TAG) ?>;
var tInt=null,tTotal=60,tLeft=60,tOn=false,currentVid='',currentNom='';
(function(){
  var g=document.getElementById('exGrid');if(!g)return;
  EX.forEach(function(e,i){
    var c=document.createElement('div');c.className='ex-card';
    c.innerHTML='<div class="ex-card-img"><img src="'+e.img+'" alt="" loading="lazy"><div class="ex-card-overlay"></div><span class="ex-diff-badge" style="background:'+e.color+'">'+e.diff+'</span></div><div class="ex-card-body"><div class="ex-card-name">'+e.nom+'</div><div class="ex-card-desc">'+e.desc+'</div><button class="ex-cta">▶ Voir + Chronomètre</button></div>';
    c.addEventListener('click',function(){openEx(i);});
    g.appendChild(c);
  });
})();
function openEx(i){
  var e=EX[i];
  currentVid=e.vid; currentNom=e.nom;
  document.getElementById('mTitle').textContent=e.nom;
  document.getElementById('mEmoji').textContent=e.diff==='Débutant'?'🟢':e.diff==='Avancé'?'🔴':'🟠';
  document.getElementById('mDesc').textContent=e.desc;
  document.getElementById('mDiff').textContent=e.diff;
  document.getElementById('mDiff').style.color=e.color;
  var iframe=document.getElementById('mIframe');
  var thumbWrap=document.getElementById('mThumbWrap');
  var fallback=document.getElementById('mFallback');
  iframe.src=''; iframe.style.display='none';
  fallback.style.display='none'; thumbWrap.style.display='block';
  var img=document.getElementById('mThumb');
  img.onerror=function(){
    thumbWrap.style.display='none'; fallback.style.display='flex';
    document.getElementById('mYtLink').href='https://www.youtube.com/results?search_query='+encodeURIComponent(e.nom+' exercice tutoriel');
  };
  img.onload=function(){
    if(this.naturalWidth<=120&&this.naturalHeight<=90){this.onerror();return;}
    thumbWrap.style.display='block';
  };
  img.src='https://img.youtube.com/vi/'+e.vid+'/hqdefault.jpg';
  resetT();
  document.getElementById('exOverlay').classList.add('show');
  document.body.style.overflow='hidden';
}
function loadVideo(){
  var iframe=document.getElementById('mIframe');
  document.getElementById('mThumbWrap').style.display='none';
  iframe.style.display='block';
  iframe.src='https://www.youtube.com/embed/'+currentVid+'?rel=0&modestbranding=1&autoplay=1';
}
function closeEx(){
  document.getElementById('exOverlay').classList.remove('show');
  document.getElementById('mIframe').src='';
  document.getElementById('mIframe').style.display='none';
  document.getElementById('mThumbWrap').style.display='block';
  pauseT(); document.body.style.overflow='';
}
document.getElementById('exOverlay').addEventListener('click',function(ev){if(ev.target===this)closeEx();});
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeEx();});
function fmt(s){var m=Math.floor(s/60),sec=s%60;return(m<10?'0':'')+m+':'+(sec<10?'0':'')+sec;}
function updT(){document.getElementById('tDisp').textContent=fmt(tLeft);document.getElementById('tBar').style.width=(tTotal>0?(tTotal-tLeft)/tTotal*100:0)+'%';}
function setT(sec,btn){resetT();tTotal=sec;tLeft=sec;updT();document.getElementById('tStatus').textContent='Durée: '+fmt(sec)+' · Prêt';document.querySelectorAll('.t-pre').forEach(function(b){b.classList.remove('active');});if(btn)btn.classList.add('active');setTimeout(startT,300);}
function startT(){if(tOn)return;tOn=true;document.getElementById('tBtnStart').style.display='none';document.getElementById('tBtnPause').style.display='inline-flex';document.getElementById('tDone').style.display='none';document.getElementById('tStatus').textContent='⏱ En cours...';tInt=setInterval(function(){if(tLeft<=0){clearInterval(tInt);tOn=false;document.getElementById('tStatus').textContent='✅ Terminé!';document.getElementById('tDone').style.display='block';document.getElementById('tBtnPause').style.display='none';document.getElementById('tBtnStart').style.display='inline-flex';document.getElementById('tBar').style.width='100%';return;}tLeft--;updT();},1000);}
function pauseT(){clearInterval(tInt);tOn=false;document.getElementById('tBtnPause').style.display='none';document.getElementById('tBtnStart').style.display='inline-flex';if(tLeft>0)document.getElementById('tStatus').textContent='⏸ En pause';}
function resetT(){pauseT();tLeft=tTotal;updT();document.getElementById('tStatus').textContent='Sélectionnez une durée';document.getElementById('tDone').style.display='none';document.getElementById('tBar').style.width='0%';document.querySelectorAll('.t-pre').forEach(function(b){b.classList.remove('active');});}
</script>
<?php
$content = ob_get_clean();
require 'views/layout_front.php';
?>
