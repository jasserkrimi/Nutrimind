/* ── AI Plan shared styles ─────────────────────────────────────── */
.ai-meta-top { font-size:.88rem; color:#888; margin-top:.3rem; }

/* Tab navigation */
.ai-tab {
  display:inline-flex; align-items:center; gap:.45rem;
  padding:.55rem 1.4rem; border-radius:30px; font-weight:600;
  font-size:.9rem; text-decoration:none; border:2px solid var(--c);
  color:var(--c); transition:all .2s;
}
.ai-tab:hover { background:var(--c); color:#fff; text-decoration:none; }
.ai-tab.active { background:var(--c); color:#fff !important;
  box-shadow:0 4px 14px rgba(0,0,0,.18); }
.ai-tab-back {
  display:inline-flex; align-items:center; gap:.45rem;
  padding:.55rem 1.4rem; border-radius:30px; font-weight:600;
  font-size:.9rem; text-decoration:none; border:2px solid #ef4444;
  color:#ef4444; transition:all .2s;
}
.ai-tab-back:hover { background:#ef4444; color:#fff; text-decoration:none; }

/* Card */
.ai-card { border-radius:20px; box-shadow:0 6px 32px rgba(0,0,0,.09);
  border:none; overflow:hidden; margin-bottom:2rem; }
.ai-card-header {
  display:flex; align-items:center; gap:1rem;
  padding:1.4rem 1.8rem;
}
.ai-card-icon { font-size:2.2rem; line-height:1; }
.ai-card-title { color:#fff; font-size:1.3rem; font-weight:700; margin:0; }
.ai-card-sub   { color:rgba(255,255,255,.8); font-size:.85rem; margin:0; }
.ai-card-body  { padding:2rem 2.2rem; background:#fff; }

/* Rendered Markdown */
.ai-h2 {
  font-size:1.2rem; font-weight:700; color:var(--accent,#6366f1);
  border-left:4px solid var(--accent,#6366f1);
  padding-left:.75rem; margin:1.6rem 0 .6rem;
}
.ai-h3 {
  font-size:1.05rem; font-weight:600; color:#374151;
  margin:1.2rem 0 .4rem;
}
.ai-h4 {
  font-size:.97rem; font-weight:600; color:#6b7280;
  margin:1rem 0 .3rem; text-transform:uppercase; letter-spacing:.04em;
}
.ai-p {
  color:#374151; line-height:1.8; margin:.4rem 0;
}
.ai-ul, .ai-ol {
  padding-left:1.4rem; margin:.4rem 0 .8rem;
}
.ai-ul li, .ai-ol li {
  color:#374151; line-height:1.75; margin-bottom:.25rem;
  position:relative;
}
.ai-ul { list-style:none; padding-left:1.2rem; }
.ai-ul li::before {
  content:"▸"; color:var(--accent,#6366f1);
  position:absolute; left:-1.1rem; font-size:.85rem; top:.15rem;
}
.ai-hr { border:none; border-top:2px solid var(--accent-light,#ede9fe);
  margin:1.2rem 0; }
.ai-code {
  background:var(--accent-light,#ede9fe); color:var(--accent,#6366f1);
  padding:.1rem .4rem; border-radius:4px; font-size:.88em;
}
