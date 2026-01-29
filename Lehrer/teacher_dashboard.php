<?php
// teacher_dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
require __DIR__ . '/../config/Datenbank.php';
require __DIR__ . '/../helpers/Rolle.php';
require_once __DIR__ . '/../models/BereichModel.php';
require_once __DIR__ . '/../models/ThemaModel.php';
require_lehrer();

$pdo = dbconnect();
$csrf = csrf_token();

$bereichModel = new BereichModel($pdo);
$themaModel = new ThemaModel($pdo);

$bereiche = $bereichModel->getAlleBereicheMitThemen();

$schulerDirFs  = __DIR__ . '/../Schuler/';
$schulerDirWeb = '../Schuler/';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Lehrer — Bereiche &amp; Themen verwalten</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="../CSS/komponente.css">

    <style>
        :root{
            --btn-bg: #236C93;
            --btn-text:#ffffff;
            --bg:#f1f7fa;
            --section-bg:#e9f6fb;
            --card-bg:#ffffff;
            --text:#12323a;
            --blue-dark:#185a73;
            --radius:12px;
            --ease:cubic-bezier(.2,.9,.2,1);
            --match-bg: #fff7c6;
            --result-border: rgba(35,108,147,0.10);
            --muted: #6b7780;
        }

        /* Reset & base */
        *{box-sizing:border-box}
        body{margin:0;font-family:Verdana, Arial, "Helvetica Neue", sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}
        .page-wrap{max-width:760px;margin:28px auto;background:var(--card-bg);border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,0.06);overflow:visible}

        /* Header */
        header.app-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;background:var(--btn-bg);color:var(--btn-text);border-top-left-radius:10px;border-top-right-radius:10px}
        .brand-left{display:flex;align-items:center;gap:12px}
        .logo{width:56px;height:56px;object-fit:cover;border-radius:8px;background:#fff;padding:4px}
        .brand-title{font-weight:700;font-size:1.15rem;letter-spacing:.4px;color:var(--btn-text)}
        .header-actions{display:flex;align-items:center;gap:12px}
        .search-box{display:flex;align-items:center;background:rgba(255,255,255,0.95);padding:6px 8px;border-radius:8px;min-width:220px}
        .search-box input{border:0;background:transparent;outline:none;font-size:0.95rem;width:170px;color:#234e5f}
        .login-icon{width:36px;height:36px;border-radius:50%;display:inline-grid;place-items:center;background:rgba(255,255,255,0.14);border:0;color:var(--btn-text)}

        /* Main */
        main.content{padding:20px 28px}
        h1.page-head{text-align:center;color:var(--btn-bg);margin:4px 0 18px;font-size:1.25rem}

        /* Section box (uses your variables) */
        .topic-section{background:var(--section-bg);border-radius:var(--radius);padding:14px;margin-bottom:18px;position:relative;border:1px solid rgba(35,108,147,0.08)}
        .topic-section h2{margin:0 0 12px;text-align:center;color:var(--blue-dark);font-weight:700}

        /* Animated wrapper (keeps content in flow) */
        .topic-content{overflow:hidden;will-change:height,opacity}
        .topic-inner{padding:10px;border-radius:8px;background:linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.95));opacity:1;transition:opacity .22s ease}

        /* Topic list static look */
        .topic-list{display:flex;flex-direction:column;gap:8px}
        .topic-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:12px 14px;
            border-radius:10px;
            background:var(--card-bg);
            border:1px solid rgba(0,0,0,0.05);
            text-decoration:none;
            color:var(--text);
            font-weight:600;
            position:relative;
            overflow:visible;
        }
        .topic-item::before{content:"";position:absolute;left:0;top:8px;bottom:8px;width:6px;border-radius:4px;background:transparent;}
        .topic-item:nth-child(6n+1){background:#eaf6ff;border-color:rgba(159,214,240,0.35)} .topic-item:nth-child(6n+1)::before{background:#9fd6f0}
        .topic-item:nth-child(6n+2){background:#e8fbf6;border-color:rgba(143,224,207,0.35)} .topic-item:nth-child(6n+2)::before{background:#8fe0cf}
        .topic-item:nth-child(6n+3){background:#fff6ea;border-color:rgba(255,208,154,0.35)} .topic-item:nth-child(6n+3)::before{background:#ffd09a}
        .topic-item:nth-child(6n+4){background:#f3eeff;border-color:rgba(205,184,255,0.35)} .topic-item:nth-child(6n+4)::before{background:#cdb8ff}
        .topic-item:nth-child(6n+5){background:#f0f8ec;border-color:rgba(156,217,168,0.35)} .topic-item:nth-child(6n+5)::before{background:#9cd9a8}
        .topic-item:nth-child(6n+6){background:#fff1f4;border-color:rgba(243,166,182,0.25)} .topic-item:nth-child(6n+6)::before{background:#f3a6b6}
        .topic-item > *{display:inline-block;vertical-align:middle}
        .topic-item .title { flex:1; padding-right:12px; }
        .topic-item:focus{outline:2px solid rgba(35,108,147,0.12);outline-offset:2px}

        /* Toggle button (chevron) bottom + top */
        .section-toggle{height:44px;width:44px;padding:8px;border-radius:10px;border:0;background:var(--card-bg);cursor:pointer;display:inline-grid;place-items:center;box-shadow:0 2px 6px rgba(0,0,0,0.06);transition:transform .18s var(--ease),box-shadow .18s var(--ease);color:var(--blue-dark)}
        .section-toggle:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,0.07)}
        .section-toggle:focus{outline:2px solid rgba(35,108,147,0.12);outline-offset:2px}
        .chev{display:inline-block;width:18px;height:18px;transition:transform .28s var(--ease);transform-origin:center;stroke:var(--blue-dark)}
        .topic-content[aria-hidden="true"] .topic-inner{opacity:0}
        .topic-content[aria-hidden="false"] .topic-inner{opacity:1}

        /* small top-toggle show only when open */
        .section-toggle.top { display: none; position: absolute; right: 14px; top: 12px; height: 40px; width: 40px; padding: 6px; border-radius: 8px; background: var(--card-bg); }
        .topic-section.section-open .section-toggle.top { display: inline-grid; }

        /* ensure bottom toggle position */
        .section-toggle.bottom { position: absolute; right: 14px; bottom: 14px; }

        /* Add-row styles (inline new topic) */
        .add-row{display:flex;gap:8px;align-items:center;margin-top:12px}
        .add-input{flex:1;padding:10px;border-radius:10px;border:1px solid rgba(0,0,0,0.08);background:var(--card-bg);font-size:0.95rem}
        .btn-ghost{background:transparent;border:1px solid rgba(0,0,0,0.06);padding:8px 10px;border-radius:8px;cursor:pointer;font-weight:700}
        .btn-save{background:var(--btn-bg);color:var(--btn-text);border:0;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:700}

        /* top layout for "add new section" */
        .add-section-wrap{display:flex;justify-content:center;margin-top:8px}
        .add-section-btn{background:var(--btn-bg);color:var(--btn-text);border:0;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;box-shadow:0 6px 20px rgba(35,108,147,0.08)}
        .add-section-btn:hover{transform:translateY(-2px)}

        /* search-results panel */
        .search-results{
            background: var(--section-bg);
            border-radius: var(--radius);
            padding: 14px;
            margin-bottom: 18px;
            border:1px solid var(--result-border);
        }
        .search-results h3{margin:0 0 8px;color:var(--blue-dark);font-weight:700}
        .search-results .count{font-weight:700;color:var(--blue-dark);margin-bottom:8px}
        .result-list{display:flex;flex-direction:column;gap:8px}
        .result-item{display:flex;justify-content:space-between;align-items:center;padding:10px 12px;border-radius:8px;background:var(--card-bg);border:1px solid rgba(0,0,0,0.05);text-decoration:none;color:var(--blue-dark);font-weight:700;cursor:pointer}
        .result-item small{color:var(--muted);font-weight:600}
        .no-results{padding:10px;border-radius:8px;background:var(--card-bg);border:1px solid rgba(0,0,0,0.05);color:var(--muted)}

        /* highlight animation for search hit */
        .search-hit {
            box-shadow: 0 0 0 3px rgba(35,108,147,0.08) inset;
            transition: box-shadow .6s ease;
        }

        /* Footer */
        footer{padding:12px;text-align:center;color:#6b7780;font-size:0.9rem}

        /* responsive */
        @media (max-width:620px){
            .page-wrap{margin:12px}
            .add-input{font-size:0.95rem}
            .section-toggle{right:10px;bottom:12px}
            .search-results{padding:12px}
        }

        /* Icon button (kleiner) */
        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 4px;
            border-radius: 6px;
            border: 0;
            background: transparent;
            cursor: pointer;
            color: var(--muted);
            transition: background .14s ease, color .14s ease, transform .08s ease;
            flex: 0 0 auto;
        }
        .icon-btn svg {
            width: 14px;
            height: 14px;
            display: block;
            pointer-events: none;
            stroke-width: 1.4;
            stroke: currentColor;
            fill: none;
        }
        .icon-btn:hover {
            background: rgba(35,108,147,0.06);
            color: var(--blue-dark);
            transform: translateY(-1px);
        }

        /* Account / Logout */
        .account { display:flex; align-items:center; gap:10px; }
        .username {
            color: var(--btn-text);
            background: rgba(255,255,255,0.12);
            padding:6px 10px;
            border-radius:8px;
            font-weight:700;
            font-size:0.95rem;
            white-space:nowrap;
        }
        .logout-form { margin:0; }
        .logout-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.14);
            color: var(--btn-text);
            padding:6px 10px;
            border-radius:8px;
            font-weight:700;
            cursor:pointer;
            transition: background .12s ease, transform .08s ease;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.06);
            transform: translateY(-2px);
        }
        @media screen and (max-width: 690px){
            .app-header{
                flex-direction: column;
            }
            .header-actions{
                flex-direction: column;
                width: 100%;
            }
            .search-box{
                width: 100%;
            }
            .account{
                width: 100%;
                justify-content: flex-end;
            }
        }

    </style>

</head>
<body>
<div class="page-wrap" role="main">
    <header class="app-header" role="banner">
        <div class="brand-left">
            <img src="/images/logo.jpg" alt="Logo" class="logo">
            <div class="brand-title">H S G G</div>
        </div>

        <div class="header-actions">
            <div class="search-box" role="search" aria-label="Seiten-Suche">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="#236C93"><path d="M21.53 20.47l-3.76-3.76A8 8 0 1 0 18 18l3.53 3.53a.75.75 0 0 0 1.06-1.06zM5.5 10.5a5 5 0 1 1 10 0 5 5 0 0 1-10 0z"/></svg>
                <input id="search" name="search" type="search" placeholder="Suchen...">
            </div>

            <?php if (!empty($_SESSION['username'])): ?>
                <div class="account">
                    <div class="username" title="Eingeloggt als"><?= htmlspecialchars($_SESSION['username']) ?></div>
                    <form method="post" action="../public/index.php?page=logout" class="logout-form" onsubmit="return confirm('Wirklich abmelden?');">
                        <button type="submit" class="logout-btn" title="Abmelden" aria-label="Abmelden">Abmelden</button>
                    </form>
                </div>
            <?php else: ?>
                <a class="login-icon" href="../views/login.php" title="Login" aria-label="Login" style="display:inline-grid;align-items:center;justify-content:center;text-decoration:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-5 0-9 2.5-9 5v1h18v-1c0-2.5-4-5-9-5z"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </header>
    <button id="menuBtn"> <img src="../images/menu-icon.png" alt="My Button" width="100" height="100"></button>
    <div id="cursorCircle"></div>
    <div id="menu" class="menu hidden" aria-hidden="true">
        <div>
            <div><label for="fontSlider">Schriftgröße (%)</label></div>
            <input id="fontSlider" type="range" min="50" max="200" value="100">
        </div>
        <div><button id="resetFont">Zurücksetzen</button></div>
        <div><button id="contrastBtn">Hoher Kontrast</button></div>
        <div><button id="toggleCircleBtn">Mauszeiger Highlight</button></div>
    </div>

    <main class="content">
        <h1 class="page-head">Bearbeitbare Lernsequenzen</h1>

        <div id="sections">
            <?php foreach ($bereiche as $b):
                $id = (int)$b['id'];
                $titel = htmlspecialchars($b['titel']);
                $themen = $b['themen'] ?? [];
                ?>
                <section class="topic-section section-open" id="section-<?= $id ?>" data-id="<?= $id ?>">
                    <div class="section-header-actions" aria-hidden="true">
                        <button class="section-delete icon-btn" data-id="<?= $id ?>" title="Bereich löschen" aria-label="Bereich löschen">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        </button>

                        <button class="section-toggle top" aria-controls="content-<?= $id ?>" aria-label="Bereich umschalten" title="Auf/Zu" data-id="<?= $id ?>">
                            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                    </div>

                    <h2><?= $titel ?></h2>

                    <div class="topic-content" id="content-<?= $id ?>" aria-hidden="false">
                        <div class="topic-inner">
                            <div class="topic-list" id="topic-list-<?= $id ?>">
                                <?php if (!empty($themen)): foreach ($themen as $t):
                                    $tid = (int)$t['id'];
                                    $ttitle = htmlspecialchars($t['titel']);
                                    ?>
                                    <!-- teacher navigates to Thema via controller -->
                                    <a class="topic-item"
                                       id="topic-<?= $tid ?>"
                                       data-id="<?= $tid ?>"
                                       data-title="<?= $ttitle ?>"
                                       data-section="<?= $id ?>"
                                       href="/public/index.php?page=thema&amp;id=<?= $tid ?>"
                                    >
                                        <div><?= $ttitle ?></div>
                                        <button class="icon-btn delete-thema"
                                                data-id="<?= $tid ?>"
                                                title="Thema löschen"
                                                aria-label="Thema löschen"
                                                onclick="event.preventDefault(); event.stopPropagation();">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                        </button>
                                    </a>
                                <?php endforeach; else: ?>
                                    <div class="topic-item notice">Noch keine Themen.</div>
                                <?php endif; ?>
                            </div>

                            <div class="add-row" id="add-row-<?= $id ?>" style="display:none;">
                                <input class="add-input" id="add-input-<?= $id ?>" placeholder="Geben Sie einen Namen ein" aria-label="Neues Thema">
                                <button class="btn-ghost cancel-new" data-id="<?= $id ?>">Abbrechen</button>
                                <button class="btn-save save-new" data-id="<?= $id ?>">Speichern</button>
                            </div>

                            <div class="add-topic-row" style="margin-top:12px;">
                                <button class="add-section-btn add-topic-trigger" data-id="<?= $id ?>">+ Neues Thema hinzufügen</button>
                            </div>
                        </div>
                    </div>

                    <button class="section-toggle bottom" aria-controls="content-<?= $id ?>" aria-label="Bereich umschalten" title="Auf/Zu" data-id="<?= $id ?>">
                        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="add-section-wrap">
            <button id="add-section-btn" class="add-section-btn">+ Neuen Abschnitt hinzufügen</button>
        </div>
    </main>
</div>
<script src="../scripts/barrierfreiheit.js"></script>

<script>
    const CSRF = <?= json_encode($csrf) ?>;

    async function postJSON(page, data){
        const url = page.indexOf('/') === 0 || page.indexOf('index.php') !== -1 ? page : '/public/index.php?page=' + encodeURIComponent(page);
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                credentials: 'same-origin'
            });
            const text = await res.text();
            try { return JSON.parse(text); } catch(e) { console.error('Server returned non-JSON', text); return { success:false, error:'Ungültige Server-Antwort (kein JSON)', raw:text }; }
        } catch (e) {
            console.error('Network error', e);
            return { success:false, error:'Netzwerkfehler' };
        }
    }

    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]); }

    function animateToggle(contentEl, open){
        if (open) {
            contentEl.style.display = 'block';
            const fullH = contentEl.scrollHeight + 'px';
            contentEl.style.height = '0px';
            requestAnimationFrame(()=> { contentEl.style.transition = 'height 320ms var(--ease)'; contentEl.style.height = fullH; });
            setTimeout(()=>{ contentEl.style.height = ''; contentEl.style.transition = ''; }, 340);
        } else {
            const curH = contentEl.scrollHeight + 'px';
            contentEl.style.height = curH;
            requestAnimationFrame(()=> { contentEl.style.transition = 'height 260ms var(--ease)'; contentEl.style.height = '0px'; });
            setTimeout(()=>{ contentEl.style.display = 'none'; contentEl.style.height = ''; contentEl.style.transition = ''; }, 280);
        }
    }

    function openSectionById(id) {
        const section = document.getElementById('section-' + id);
        if (!section) return;
        const content = document.getElementById('content-' + id);
        if (!content) return;
        if (content.getAttribute('aria-hidden') === 'true') {
            content.setAttribute('aria-hidden','false');
            section.classList.add('section-open');
            animateToggle(content, true);
        }
    }

    /* ========== helpers for binding topic delete (used widely) ========== */
    function bindDelete(btn){
        if (!btn) return;
        if (btn._bound) return;
        btn._bound = true;
        btn.addEventListener('click', async (e) => {
            const id = btn.dataset.id;
            if (!confirm('Thema wirklich löschen?')) return;
            btn.disabled = true;
            const res = await postJSON('delete_thema', { id: id, csrf: CSRF });
            btn.disabled = false;
            if (!res.success) { alert(res.error || 'Fehler beim Löschen'); return; }
            const node = document.getElementById('topic-' + id);
            if (node) node.remove();
        });
    }

    /* Delete Bereich binding (für vorhandene Buttons) */
    function bindDeleteBereichBtn(btn){
        if (!btn) return;
        if (btn._bound) return;
        btn._bound = true;
        btn.addEventListener('click', async (e) => {
            const id = btn.dataset.id;
            if (!confirm('Fach wirklich löschen?\nAlle enthaltenen Themen gehen verloren.')) return;
            btn.disabled = true;
            const res = await postJSON('delete_bereich', { id: id, csrf: CSRF });
            btn.disabled = false;
            if (!res.success) { alert(res.error || 'Fehler beim Löschen'); console.error('delete_bereich failed', res); return; }
            const sec = document.getElementById('section-' + id);
            if (sec) sec.remove();
        });
    }

    /* ========== BIND EXISTING SECTIONS (toggles, add-topic row, delete topic) ========== */
    document.querySelectorAll('.topic-section').forEach(section => {
        const id = section.dataset.id;
        const content = document.getElementById('content-' + id);
        const topBtn = section.querySelector('.section-toggle.top');
        const bottomBtn = section.querySelector('.section-toggle.bottom');

        const addRow = document.getElementById('add-row-' + id);
        if (addRow) addRow.style.display = 'none';

        function setOpen(open){
            if (open) {
                content.setAttribute('aria-hidden', 'false');
                section.classList.add('section-open');
                animateToggle(content, true);
            } else {
                content.setAttribute('aria-hidden', 'true');
                section.classList.remove('section-open');
                animateToggle(content, false);
            }
        }

        const initiallyOpen = content.getAttribute('aria-hidden') === 'false';
        if (!initiallyOpen) { content.style.display = 'none'; }

        [topBtn, bottomBtn].forEach(btn => {
            if (!btn) return;
            btn.addEventListener('click', () => {
                const hidden = content.getAttribute('aria-hidden') === 'true';
                setOpen(hidden);
            });
        });

        // add-topic trigger -> shows add-row
        section.querySelectorAll('.add-topic-trigger').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!addRow) return;
                addRow.style.display = 'flex';
                const inp = addRow.querySelector('.add-input');
                inp && inp.focus();
                inp && inp.select && inp.select();
            });
        });

        // add-row cancel
        if (addRow) {
            const cancelBtn = addRow.querySelector('.cancel-new');
            if (cancelBtn && !cancelBtn._bound) {
                cancelBtn._bound = true;
                cancelBtn.addEventListener('click', () => {
                    addRow.style.display = 'none';
                    const ip = addRow.querySelector('.add-input');
                    if (ip) ip.value = '';
                });
            }
            // add-row save (create thema)
            const saveBtn = addRow.querySelector('.save-new');
            if (saveBtn && !saveBtn._bound) {
                saveBtn._bound = true;
                saveBtn.addEventListener('click', async (e) => {
                    const titleInput = addRow.querySelector('.add-input');
                    const title = (titleInput?.value || '').trim();
                    if (!title) { alert('Bitte Titel eingeben'); titleInput?.focus(); return; }
                    saveBtn.disabled = true;
                    const res = await postJSON('save_thema', { action:'create', bereich_id: id, titel: title, csrf: CSRF });
                    saveBtn.disabled = false;
                    if (!res.success) { alert(res.error || 'Fehler'); console.error('save_thema failed', res); return; }

                    const list = document.getElementById('topic-list-' + id);
                    const notice = list && list.querySelector('.notice');
                    if (notice && !list.querySelector('.topic-item')) list.innerHTML = '';

                    const a = document.createElement('a');
                    a.className = 'topic-item';
                    a.id = 'topic-' + res.id;
                    a.dataset.id = res.id;
                    a.dataset.title = title;
                    a.dataset.section = id;
                    a.href = '/public/index.php?page=thema&id=' + res.id;
                    a.innerHTML = `<div>${escapeHtml(title)}</div>
                        <button class="icon-btn delete-thema" data-id="${res.id}" title="Thema löschen" aria-label="Thema löschen" onclick="event.preventDefault(); event.stopPropagation();">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        </button>`;
                    list.appendChild(a);
                    bindDelete(a.querySelector('.delete-thema'));
                    if (titleInput) titleInput.value = '';
                    addRow.style.display = 'none';
                });
            }
        }

        // bind delete buttons of existing topics
        section.querySelectorAll('.delete-thema').forEach(btn => bindDelete(btn));

        // bind delete-bereich button if present in header actions
        const delBereichBtn = section.querySelector('.section-delete');
        if (delBereichBtn) bindDeleteBereichBtn(delBereichBtn);
    });

    /* ========== Add section inline (create new Bereich) ========== */
    document.getElementById('add-section-btn').addEventListener('click', (e) => {
        const container = document.getElementById('sections');
        if (document.querySelector('.new-section-inline')) {
            document.querySelector('.new-section-inline .new-section-input').focus();
            return;
        }
        const wrapper = document.createElement('section');
        wrapper.className = 'topic-section new-section-inline';
        wrapper.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:12px;height:12px;border-radius:50%;background:var(--btn-bg)"></div>
              <input class="new-section-input" placeholder="Geben Sie einen Namen ein" style="flex:1;padding:10px;border-radius:10px;border:1px solid rgba(0,0,0,0.06)" />
              <div style="display:flex;gap:8px">
                <button class="btn-ghost new-section-cancel">Abbrechen</button>
                <button class="btn-save new-section-save">Speichern</button>
              </div>
            </div>
        `;
        container.appendChild(wrapper);
        const input = wrapper.querySelector('.new-section-input');
        input.focus(); input.select();
        wrapper.querySelector('.new-section-cancel').addEventListener('click', () => wrapper.remove());

        wrapper.querySelector('.new-section-save').addEventListener('click', async (ev) => {
            const title = input.value.trim();
            if (!title) { alert('Bitte Titel eingeben'); input.focus(); return; }
            const btn = ev.currentTarget;
            btn.disabled = true;
            const res = await postJSON('save_bereich', { action:'create', titel: title, csrf: CSRF });
            btn.disabled = false;
            if (!res.success) { alert(res.error || 'Fehler beim Anlegen'); console.error('save_bereich error', res); return; }
            wrapper.remove();
            const newId = res.id;

            const secHtml = `
              <section class="topic-section section-open" id="section-${newId}" data-id="${newId}">
                <div class="section-header-actions" aria-hidden="true">
                  <button class="section-delete icon-btn" data-id="${newId}" title="Bereich löschen" aria-label="Bereich löschen">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                  </button>
                  <button class="section-toggle top" aria-controls="content-${newId}" data-id="${newId}"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                </div>
                <h2>${escapeHtml(title)}</h2>
                <div class="topic-content" id="content-${newId}" aria-hidden="false">
                  <div class="topic-inner">
                    <div class="topic-list" id="topic-list-${newId}"><div class="topic-item notice">Noch keine Themen.</div></div>
                    <div class="add-row" id="add-row-${newId}" style="display:none;">
                      <input class="add-input" /><button class="btn-ghost cancel-new">Abbrechen</button><button class="btn-save save-new">Speichern</button>
                    </div>
                    <div class="add-topic-row"><button class="add-section-btn add-topic-trigger" data-id="${newId}">+ Neues Thema hinzufügen</button></div>
                  </div>
                </div>
                <button class="section-toggle bottom" aria-controls="content-${newId}" data-id="${newId}"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
              </section>`;
            container.insertAdjacentHTML('beforeend', secHtml);

            // bind new section handlers (toggles + delete + add-row handlers)
            const newSection = document.getElementById('section-' + newId);
            if (newSection) {
                const topBtn = newSection.querySelector('.section-toggle.top');
                const bottomBtn = newSection.querySelector('.section-toggle.bottom');
                const content = document.getElementById('content-' + newId);

                [topBtn, bottomBtn].forEach(b => b && b.addEventListener('click', ()=> {
                    const hidden = content.getAttribute('aria-hidden') === 'true';
                    if (hidden) { content.setAttribute('aria-hidden','false'); newSection.classList.add('section-open'); animateToggle(content, true); }
                    else { content.setAttribute('aria-hidden','true'); newSection.classList.remove('section-open'); animateToggle(content, false); }
                }));

                // bind add-topic trigger (shows add-row)
                newSection.querySelectorAll('.add-topic-trigger').forEach(btn => btn.addEventListener('click', ()=> {
                    const addRowLocal = document.getElementById('add-row-' + newId);
                    if (addRowLocal) {
                        addRowLocal.style.display = 'flex';
                        const ip = addRowLocal.querySelector('.add-input');
                        ip?.focus();
                        ip?.select?.();
                    }
                }));

                // bind delete-bereich
                const delBtn = newSection.querySelector('.section-delete');
                delBtn && bindDeleteBereichBtn(delBtn);

                //  bind add-row controls (Cancel + Save) for THIS NEW SECTION
                const addRowLocal = document.getElementById('add-row-' + newId);
                if (addRowLocal) {
                    addRowLocal.style.display = 'none';
                    const cancelBtn = addRowLocal.querySelector('.cancel-new');
                    if (cancelBtn && !cancelBtn._bound) {
                        cancelBtn._bound = true;
                        cancelBtn.addEventListener('click', () => {
                            addRowLocal.style.display = 'none';
                            const ip = addRowLocal.querySelector('.add-input');
                            if (ip) ip.value = '';
                        });
                    }

                    const saveBtn = addRowLocal.querySelector('.save-new');
                    if (saveBtn && !saveBtn._bound) {
                        saveBtn._bound = true;
                        saveBtn.addEventListener('click', async (e) => {
                            const titleInput = addRowLocal.querySelector('.add-input');
                            const title = (titleInput?.value || '').trim();
                            if (!title) { alert('Bitte Titel eingeben'); titleInput?.focus(); return; }

                            saveBtn.disabled = true;
                            const r = await postJSON('save_thema', { action:'create', bereich_id: newId, titel: title, csrf: CSRF });
                            saveBtn.disabled = false;
                            if (!r.success) { alert(r.error || 'Fehler'); console.error('save_thema failed', r); return; }

                            // append new topic to list
                            const list = document.getElementById('topic-list-' + newId);
                            const notice = list && list.querySelector('.notice');
                            if (notice && !list.querySelector('.topic-item')) list.innerHTML = '';

                            const a = document.createElement('a');
                            a.className = 'topic-item';
                            a.id = 'topic-' + r.id;
                            a.dataset.id = r.id;
                            a.dataset.title = title;
                            a.dataset.section = newId;
                            a.href = '/public/index.php?page=thema&id=' + r.id;
                            a.innerHTML = `<div>${escapeHtml(title)}</div>
                                <button class="icon-btn delete-thema" data-id="${r.id}" title="Thema löschen" aria-label="Thema löschen" onclick="event.preventDefault(); event.stopPropagation();">
                                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>`;
                            list.appendChild(a);
                            bindDelete(a.querySelector('.delete-thema'));

                            // reset & hide
                            if (titleInput) titleInput.value = '';
                            addRowLocal.style.display = 'none';
                        });
                    }
                }
            }

            // scroll to new section so user sees it immediately
            const justAdded = document.getElementById('section-' + newId);
            justAdded && justAdded.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    /* ========== Delete Bereich binding for any dynamically added ones already present ========== */
    document.querySelectorAll('.section-delete').forEach(btn => bindDeleteBereichBtn(btn));

    /* ========== Search (builds index from DOM including new inserted nodes) ========== */
    (function(){
        const searchInput = document.getElementById('search');
        const sectionsContainer = document.getElementById('sections');

        // ensure search-results panel exists (wenn nicht, erstellen)
        let resultsPanel = document.getElementById('search-results');
        let resultsCount = document.getElementById('results-count');
        let resultList = document.getElementById('result-list');

        function createResultsPanel() {
            const panel = document.createElement('div');
            panel.id = 'search-results';
            panel.className = 'search-results';
            panel.style.display = 'none';
            panel.innerHTML = `
                <h3>Suchergebnisse</h3>
                <div class="count" id="results-count">Suchergebnisse: 0</div>
                <div class="result-list" id="result-list"></div>
            `;
            if (sectionsContainer && sectionsContainer.parentNode) {
                sectionsContainer.parentNode.insertBefore(panel, sectionsContainer);
            } else {
                document.body.appendChild(panel);
            }
            return {
                panel: document.getElementById('search-results'),
                count: document.getElementById('results-count'),
                list: document.getElementById('result-list')
            };
        }

        if (!resultsPanel || !resultsCount || !resultList) {
            const created = createResultsPanel();
            resultsPanel = created.panel;
            resultsCount = created.count;
            resultList = created.list;
        }

        function dbg(...args){ console.debug('[TEACHER-SEARCH]', ...args); }

        function escapeRegExp(s){ return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
        function debounce(fn, wait=200){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }

        function buildIndex(){
            const records = [];
            document.querySelectorAll('.topic-section').forEach(sec => {
                const secId = sec.dataset.id || null;
                const secTitle = (sec.querySelector('h2')?.textContent || '').trim();
                if (secTitle) records.push({ type: 'section', text: secTitle, sectionId: secId, el: sec, href: null });

                sec.querySelectorAll('.topic-item').forEach(item => {
                    let title = (item.dataset && item.dataset.title) ? item.dataset.title.trim() : '';
                    if (!title) {
                        const tEl = item.querySelector('.title');
                        if (tEl) title = (tEl.textContent || '').trim();
                    }
                    if (!title) {
                        const clone = item.cloneNode(true);
                        clone.querySelectorAll('button, .icon-btn').forEach(n => n.remove());
                        title = (clone.textContent || '').trim();
                    }
                    let href = null;
                    if (item.tagName && item.tagName.toLowerCase() === 'a') {
                        href = item.getAttribute('href') || null;
                    } else {
                        const a = item.querySelector('a');
                        href = a ? (a.getAttribute('href') || null) : null;
                    }
                    if (!title) return;
                    records.push({ type: 'topic', text: title, sectionId: secId, el: item, href: href });
                });
            });
            dbg('Index aufgebaut — Einträge:', records.length);
            return records;
        }

        let indexCache = buildIndex();

        function renderResults(matches, query) {
            if (!query) {
                if (resultsPanel) resultsPanel.style.display = 'none';
                if (sectionsContainer) sectionsContainer.style.display = '';
                return;
            }
            if (sectionsContainer) sectionsContainer.style.display = 'none';
            if (resultsPanel) resultsPanel.style.display = '';
            resultsCount.textContent = 'Suchergebnisse: ' + matches.length;
            resultList.innerHTML = '';
            if (matches.length === 0) {
                const no = document.createElement('div'); no.className = 'no-results'; no.textContent = 'Keine Ergebnisse. Versuchen Sie einen anderen Begriff.'; resultList.appendChild(no);
                return;
            }

            const re = new RegExp('(' + escapeRegExp(query) + ')','ig');

            matches.forEach(m => {
                const row = document.createElement('div');
                row.className = 'result-item';
                const left = document.createElement('div');
                left.style.display = 'flex';
                left.style.flexDirection = 'column';
                const title = document.createElement('div');
                title.innerHTML = (m.text || '').replace(re, '<mark>$1</mark>');
                const meta = document.createElement('small');
                meta.textContent = m.type === 'section' ? 'Bereich' : ('Thema — ' + (document.querySelector('#section-' + m.sectionId + ' h2')?.textContent || ''));
                left.appendChild(title);
                left.appendChild(meta);
                row.appendChild(left);

                row.addEventListener('click', () => {
                    if (resultsPanel) resultsPanel.style.display = 'none';
                    if (sectionsContainer) sectionsContainer.style.display = '';
                    if (m.sectionId) openSectionById(m.sectionId);
                    if (m.type === 'topic') {
                        const el = document.getElementById(m.el.id);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            el.classList.add('search-hit');
                            setTimeout(()=> el.classList.remove('search-hit'), 1500);
                        }
                        if (m.href) {
                            dbg('Navigate to', m.href);
                            setTimeout(()=> window.location.href = m.href, 300);
                        }
                    } else {
                        const sec = document.getElementById('section-' + m.sectionId);
                        if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });

                resultList.appendChild(row);
            });
        }

        function doSearch(q) {
            const term = (q || '').trim();
            if (!term) { renderResults([], ''); return; }
            indexCache = buildIndex(); // rebuild to include freshly added sections/topics
            const low = term.toLowerCase();
            const matches = indexCache.filter(r => r.text && r.text.toLowerCase().includes(low));
            dbg('Search:', term, 'Treffer:', matches.length);
            renderResults(matches, term);
        }

        const debounced = debounce((e) => doSearch(e.target.value), 200);
        if (searchInput) {
            searchInput.addEventListener('input', debounced);
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { searchInput.value = ''; doSearch(''); searchInput.blur(); }
                if (e.key === 'Enter') { e.preventDefault(); doSearch(searchInput.value); }
            });
        } else {
            dbg('Kein Suchfeld (#search) gefunden.');
        }

        // Expose for debugging:
        window._teacherSearch = { buildIndex, doSearch };
        dbg('Teacher-Search initialisiert');
    })();
</script>

</body>
</html>
