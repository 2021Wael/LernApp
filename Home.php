<?php
// Home.php (mit exakten Pfaden für vorhandene Themen)
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require __DIR__ . '/config/Datenbank.php'; // anpassen, falls die Datei woanders liegt

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{Nd}]+/u', '_', $text);
    $text = trim($text, '_');
    return mb_substr($text, 0, 60, 'UTF-8');
}

// --> Hier die exakten Dateinamen, die du angegeben hast:
$fixedFilenames = [
        1  => '1_addition_subtraktion.php',
        2  => '2_multiplikation_division.php',
        3  => '3_bruchrechnung.php',
        4  => '4_erweitern_kuerzen_von_bruechen.php',
        5  => '5_geo_berechnung_von_flaechen.php',
        6  => '6_geo_umfang_von_figuren.php',
        7  => '7_geo_flaeche_von_dreiecken.php',
        8  => '8_geo_kreise.php',
        9  => '9_geo_volumen_von_quadern.php',
        10 => '10_dezimalsystem.php',
        11 => '11_dezimalzahlen_runden.php',
        12 => '12_prozentrechnung.php',
        13 => '13_zinsrechnung.php',
        14 => '14_wahrscheinlichkeitsrechnung.php',
        15 => '15_graphen_diagramme.php',
];

try {
    $pdo = dbconnect();
    $stmt = $pdo->query('
        SELECT b.id AS bereich_id, b.titel AS bereich_titel, t.id AS thema_id, t.titel AS thema_titel
        FROM bereiche b
        LEFT JOIN themen t ON t.bereich_id = b.id
        ORDER BY b.id, t.id
    ');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $bereiche = [];
    foreach ($rows as $r) {
        $bid = (int)$r['bereich_id'];
        if (!isset($bereiche[$bid])) {
            $bereiche[$bid] = [
                    'id' => $bid,
                    'titel' => $r['bereich_titel'],
                    'themen' => []
            ];
        }
        if (!empty($r['thema_id'])) {
            $tid = (int)$r['thema_id'];
            $titel = $r['thema_titel'];

            // wenn ID in fixedFilenames, benutze exakten Namen, sonst Fallback zu slug-basiertem Dateinamen
            if (array_key_exists($tid, $fixedFilenames)) {
                $href = 'Schuler/' . $fixedFilenames[$tid];
            } else {
                $slug = slugify($titel);
                $href = 'Schuler/' . $tid . ($slug ? '_' . $slug : '') . '.php';
            }

            $bereiche[$bid]['themen'][] = [
                    'id' => $tid,
                    'titel' => $titel,
                    'href' => $href
            ];
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Fehler beim Laden der Inhalte.";
    exit;
}
?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Lern App — Startseite</title>
    <link rel="stylesheet" href="public/CSS/Home.css">
</head>
<body>
<div class="page-wrap" role="main">
    <header class="app-header" role="banner">
        <div class="brand-left">
            <img src="public/images/logo.jpg" alt="Logo" class="logo">
            <div class="brand-title">H S G G</div>
        </div>

        <div class="header-actions">
            <div class="search-box" role="search" aria-label="Seiten-Suche">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="#236C93"><path d="M21.53 20.47l-3.76-3.76A8 8 0 1 0 18 18l3.53 3.53a.75.75 0 0 0 1.06-1.06zM5.5 10.5a5 5 0 1 1 10 0 5 5 0 0 1-10 0z"/></svg>
                <input id="search" name="search" type="search" placeholder="Suchen..." aria-label="Suchen">
            </div>

            <div style="display:flex;gap:10px;align-items:center">
                <a class="btn-ghost" href="views/login.php" title="Login">Anmelden</a>
                <a class="btn-primary" href="views/register.php" title="Registrieren">Registrieren</a>
            </div>
        </div>
    </header>

    <main class="content" role="main">
        <h1 class="page-head">Unsere Lernsequenzen</h1>

        <div id="search-results" class="search-results" style="display:none;">
            <h3>Suchergebnisse</h3>
            <div class="count" id="results-count">Suchergebnisse: 0</div>
            <div class="result-list" id="result-list"></div>
            <div style="margin-top:8px"><button id="clear-search" class="clear-search">Suche zurücksetzen</button></div>
        </div>

        <div id="sections">
            <?php foreach ($bereiche as $b): $bid = (int)$b['id']; ?>
                <section class="topic-section section-open" id="section-<?= $bid ?>" data-id="<?= $bid ?>">
                    <button class="section-toggle top" data-target="content-<?= $bid ?>" aria-controls="content-<?= $bid ?>" aria-expanded="true" title="Zusammenklappen (oben)">
                        <svg class="chev" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <h2><?= htmlspecialchars($b['titel']) ?></h2>

                    <div class="topic-content" id="content-<?= $bid ?>" aria-hidden="false">
                        <div class="topic-inner">
                            <div class="topic-list" id="topic-list-<?= $bid ?>">
                                <?php if (!empty($b['themen'])): foreach ($b['themen'] as $t): ?>
                                    <div class="topic-item" id="topic-<?= (int)$t['id'] ?>" data-id="<?= (int)$t['id'] ?>" data-title="<?= htmlspecialchars($t['titel']) ?>">
                                        <a href="<?= htmlspecialchars($t['href']) ?>">
                                            <span><?= htmlspecialchars($t['titel']) ?></span>
                                        </a>
                                    </div>
                                <?php endforeach; else: ?>
                                    <div class="topic-item notice">Noch keine Themen.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <button class="section-toggle bottom" data-target="content-<?= $bid ?>" aria-controls="content-<?= $bid ?>" aria-expanded="true" title="Auf/Zu (unten)">
                        <svg class="chev" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                </section>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>&copy; 2025 Lern App</footer>
</div>

<script>
    /* Toggle animation + top/bottom + Suche (wie vorher, aber nutzt href aus DOM) */
    (function(){
        const sections = Array.from(document.querySelectorAll('.topic-section'));

        function easeOutCubic(t){ return 1 - Math.pow(1 - t, 3); }
        function animateHeight(el, from, to, duration=360){
            const start = performance.now();
            return new Promise(resolve => {
                function frame(now){
                    const t = Math.min(1, (now - start) / duration);
                    const eased = easeOutCubic(t);
                    el.style.height = Math.round(from + (to - from) * eased) + 'px';
                    if (t < 1) requestAnimationFrame(frame); else resolve();
                }
                requestAnimationFrame(frame);
            });
        }

        async function openSection(section, content){
            const startH = content.clientHeight || 0;
            content.style.display = 'block';
            content.style.height = startH + 'px';
            const fullH = content.scrollHeight;
            section.classList.add('section-open');
            content.setAttribute('aria-hidden', 'false');
            await animateHeight(content, startH, fullH, 360);
            content.style.height = '';
        }

        async function closeSection(section, content){
            const startH = content.scrollHeight;
            content.style.height = startH + 'px';
            section.classList.remove('section-open');
            content.setAttribute('aria-hidden', 'true');
            await animateHeight(content, startH, 0, 300);
            content.style.display = 'none';
            content.style.height = '';
        }

        sections.forEach(section => {
            const id = section.dataset.id;
            const content = document.getElementById('content-' + id);
            const topBtn = section.querySelector('.section-toggle.top');
            const bottomBtn = section.querySelector('.section-toggle.bottom');

            const initiallyHidden = content.getAttribute('aria-hidden') === 'true';
            if (initiallyHidden) content.style.display = 'none';

            function toggle(){
                const hidden = content.getAttribute('aria-hidden') === 'true';
                if (hidden) openSection(section, content).catch(()=>{}); else closeSection(section, content).catch(()=>{});
                const newOpen = content.getAttribute('aria-hidden') === 'false';
                [topBtn, bottomBtn].forEach(b => { if (b) b.setAttribute('aria-expanded', newOpen ? 'true' : 'false'); });
            }

            [topBtn, bottomBtn].forEach(btn => {
                if (!btn) return;
                btn.addEventListener('click', (e) => { e.preventDefault(); toggle(); });
                btn.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }});
            });
        });

        /* SEARCH */
        const searchInput = document.getElementById('search');
        const resultsPanel = document.getElementById('search-results');
        const resultsCount = document.getElementById('results-count');
        const resultList = document.getElementById('result-list');
        const clearBtn = document.getElementById('clear-search');

        function buildIndex(){
            const records = [];
            document.querySelectorAll('.topic-section').forEach(sec => {
                const secId = sec.dataset.id;
                const secTitle = (sec.querySelector('h2')?.textContent || '').trim();
                records.push({ type:'section', text: secTitle, sectionId: secId, el: sec, href: null });
                sec.querySelectorAll('.topic-item').forEach(item => {
                    const title = item.dataset.title || (item.textContent || '').trim();
                    const a = item.querySelector('a');
                    const href = a ? a.getAttribute('href') : null;
                    records.push({ type:'topic', text: title, sectionId: secId, el: item, href: href });
                });
            });
            return records;
        }

        function escapeRegExp(s){ return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
        function debounce(fn, wait=220){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }

        function renderResults(matches, term){
            if (!term) {
                resultsPanel.style.display = 'none';
                document.getElementById('sections').style.display = '';
                return;
            }
            document.getElementById('sections').style.display = 'none';
            resultsPanel.style.display = '';
            resultsCount.textContent = 'Suchergebnisse: ' + matches.length;
            resultList.innerHTML = '';
            if (matches.length === 0) {
                const no = document.createElement('div'); no.className='result-item'; no.textContent='Keine Ergebnisse.'; resultList.appendChild(no); return;
            }
            const re = new RegExp('(' + escapeRegExp(term) + ')','ig');
            matches.forEach(m => {
                const row = document.createElement('div'); row.className='result-item';
                const left = document.createElement('div'); left.style.display='flex'; left.style.flexDirection='column';
                const title = document.createElement('div'); title.innerHTML = m.text.replace(re, '<mark>$1</mark>');
                const meta = document.createElement('small'); meta.textContent = m.type === 'section' ? 'Bereich' : ('Thema — ' + (document.querySelector('#section-' + m.sectionId + ' h2')?.textContent || ''));
                left.appendChild(title); left.appendChild(meta);
                row.appendChild(left);
                row.addEventListener('click', () => {
                    resultsPanel.style.display = 'none';
                    document.getElementById('sections').style.display = '';
                    const sec = document.getElementById('section-' + m.sectionId);
                    const content = document.getElementById('content-' + m.sectionId);
                    if (content && content.getAttribute('aria-hidden') === 'true') {
                        const bottom = sec.querySelector('.section-toggle.bottom');
                        bottom && bottom.click();
                    }
                    if (m.type === 'topic') {
                        const el = document.getElementById(m.el.id);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            el.classList.add('mark-new');
                            setTimeout(()=> el.classList.remove('mark-new'), 1400);
                        }
                        // falls href vorhanden: nach kurzer Verzögerung direkt zum Thema navigieren
                        if (m.href) {
                            setTimeout(()=> { window.location.href = m.href; }, 550);
                        }
                    } else {
                        sec && sec.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                resultList.appendChild(row);
            });
        }

        let indexCache = buildIndex();
        function doSearch(q){
            const term = (q || '').trim();
            if (!term) { renderResults([], ''); return; }
            indexCache = buildIndex();
            const low = term.toLowerCase();
            const matches = indexCache.filter(r => r.text.toLowerCase().includes(low));
            renderResults(matches, term);
        }

        const debounced = debounce((e) => doSearch(e.target.value), 180);
        if (searchInput) {
            searchInput.addEventListener('input', debounced);
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { searchInput.value=''; doSearch(''); searchInput.blur(); }
                if (e.key === 'Enter') { e.preventDefault(); doSearch(searchInput.value); }
            });
        }
        if (clearBtn) {
            clearBtn.addEventListener('click', () => { searchInput.value = ''; doSearch(''); searchInput.focus(); });
        }
    })();
</script>
</body>
</html>
