<?php
/**
 * PROJET-CMS-2026 - ÉDITEUR DESIGN SYSTEM (RESTO)
 * @author: Christophe Millot
 */
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { die("Acces reserve."); exit; }

$content_dir = "../content/";
$slug = isset($_GET['slug']) ? $_GET['slug'] : 'nouveau-projet';
$title = "Titre du Projet";
$category = "Design";
$summary = "";
$htmlContent = "";

if (file_exists($content_dir . $slug . '/data.php')) {
    include $content_dir . $slug . '/data.php';
}

$safeHtml = str_replace(["\r", "\n"], '', addslashes($htmlContent));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditeur Pro - CMS 2026</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap">
    <style id="dynamic-styles"></style>
    <style>
        /* --- 0. CUSTOM SCROLLBAR (TRAIT FIN BLEU) --- */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #007bff; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #0056b3; }

        /* --- 1. CONFIGURATION DES THÈMES --- */
        :root {
            --sidebar-bg: #111111;
            --sidebar-border: #333333;
            --sidebar-text: #ffffff;
            --sidebar-muted: #666666;
            --sidebar-input: #1a1a1a;
            --canvas-bg: #1a1a1a;
            --accent: #ffffff;
        }

        body.light-mode { --canvas-bg: #e0e0e0; --accent: #000000; }
        
        body { 
            margin: 0; font-family: 'Inter', sans-serif; background-color: var(--canvas-bg); 
            color: var(--accent); display: flex; height: 100vh; width: 100vw; overflow: hidden; transition: background 0.3s; 
        }
        
        .sidebar { 
            position: fixed; top: 0; left: 0; bottom: 0; width: 340px; 
            background-color: #111111; border-right: 1px solid var(--sidebar-border); 
            display: flex; flex-direction: column; z-index: 1000; color: #ffffff;
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
        }
        body.sidebar-hidden .sidebar { transform: translateX(-100%); }
        
        .sidebar-header { padding: 40px 25px 25px; border-bottom: 1px solid var(--sidebar-border); display: flex; align-items: center; gap: 15px; }
        .sidebar-header h2 { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; margin: 0; color: var(--sidebar-muted); flex-grow: 1; }
        
        .sidebar-scroll { flex-grow: 1; overflow-y: auto; padding: 20px 25px; }
        
        .sidebar-footer { padding: 25px; border-top: 1px solid var(--sidebar-border); background-color: #111111; display: flex; flex-direction: column; gap: 10px; }

        .admin-input { width: 100%; background-color: var(--sidebar-input); border: 1px solid var(--sidebar-border); color: var(--sidebar-text); padding: 12px; margin-bottom: 12px; font-size: 11px; border-radius: 4px; outline: none; box-sizing: border-box; resize: vertical; }

        .section-label { font-size: 9px; color: var(--sidebar-muted); text-transform: uppercase; margin-top: 25px; margin-bottom: 10px; display: block; }

        .grid-structure { display: flex; flex-direction: column; gap: 8px; }
        .row-h { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .row-styles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .row-align { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .row-float { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 8px; }

        .tool-btn { 
            background-color: var(--sidebar-input); border: 1px solid var(--sidebar-border); 
            color: var(--sidebar-muted); height: 40px; cursor: pointer; font-size: 10px; font-weight: bold;
            border-radius: 4px; transition: 0.2s; text-transform: uppercase;
            display: flex; align-items: center; justify-content: center; width: 100%;
        }
        .tool-btn:hover { border-color: #555; color: #fff; }
        .tool-btn svg { width: 16px; height: 16px; pointer-events: none; }

        .color-wrapper {
            position: relative; width: 100%; height: 40px;
            border: 1px solid var(--sidebar-border); border-radius: 4px;
            overflow: hidden; cursor: pointer; box-sizing: border-box;
            background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);
        }
        .color-wrapper:hover { border-color: #fff; }
        .color-wrapper input[type="color"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

        .gauge-row { background-color: var(--sidebar-input); padding: 15px; border-radius: 6px; margin-bottom: 10px; border: 1px solid var(--sidebar-border); }
        .gauge-info { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 10px; color: var(--sidebar-muted); }
        .gauge-data { color: var(--sidebar-text); font-family: monospace; }

        .canvas { 
            flex-grow: 1; height: 100vh; overflow-y: auto; display: block; transition: padding-left 0.4s; box-sizing: border-box; padding: 80px 20px; 
        }
        body:not(.sidebar-hidden) .canvas { padding-left: 360px; }

        .paper { 
            width: 100%; max-width: 850px; background: #ffffff; color: #000000; min-height: 1100px; padding: 100px; 
            box-shadow: 0 40px 100px rgba(0,0,0,0.5); display: block; box-sizing: border-box; margin: 0 auto; position: relative;
        }

        .paper::after { content: ""; display: table; clear: both; }

        .block-container { position: relative; margin-bottom: 5px; width: 100%; box-sizing: border-box; clear: both; }
        .delete-block { position: absolute; left: -18px; top: 0; background: #ff4d4d; color: white; width: 18px; height: 18px; border-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 9px; cursor: pointer; opacity: 0; transition: opacity 0.2s; z-index: 10; }
        .block-container:hover .delete-block { opacity: 1; }

        .float-block { overflow: hidden; margin-bottom: 20px; width: 100%; }
        
        .grid-block { display: grid; margin-bottom: 20px; width: 100%; clear: both; }
        .grid-item { background: transparent; padding: 0; box-sizing: border-box; min-width: 0; }

        .theme-toggle { cursor: pointer; font-size: 16px; color: #ffffff; }
        .sidebar-trigger { position: fixed; top: 20px; left: 20px; z-index: 500; background: var(--accent); color: var(--canvas-bg); border: none; width: 40px; height: 40px; border-radius: 4px; cursor: pointer; font-weight: bold; transition: 0.3s; }
    </style>
</head>
<body>

    <button class="sidebar-trigger" onclick="toggleSidebar()">☰</button>

    <aside class="sidebar">
        <div class="sidebar-header">
            <span style="color:#ff4d4d; cursor:pointer; font-weight:bold;" onclick="toggleSidebar()">✕</span>
            <h2>PROJET STUDIO</h2>
            <div class="theme-toggle" onclick="toggleTheme()" id="t-icon">🌙</div>
        </div>

        <div class="sidebar-scroll">
            <span class="section-label">MÉTADONNÉES</span>
            <input type="text" id="inp-slug" class="admin-input" placeholder="Slug" value="<?php echo $slug; ?>">
            <textarea id="inp-summary" class="admin-input" placeholder="Résumé" style="height:60px;"><?php echo $summary; ?></textarea>

            <span class="section-label">TYPOGRAPHIE</span>
            <div class="grid-structure">
                <div class="row-h">
                    <button class="tool-btn" onclick="addBlock('h1', 'Titre H1')">H1</button>
                    <button class="tool-btn" onclick="addBlock('h2', 'Titre H2')">H2</button>
                    <button class="tool-btn" onclick="addBlock('h3', 'Titre H3')">H3</button>
                    <button class="tool-btn" onclick="addBlock('h4', 'Titre H4')">H4</button>
                    <button class="tool-btn" onclick="addBlock('h5', 'Titre H5')">H5</button>
                </div>
                
                <button class="tool-btn" onclick="addBlock('p')">Paragraphe</button>
                
                <div class="row-styles">
                    <button class="tool-btn" onclick="execStyle('bold')">B</button>
                    <button class="tool-btn" onclick="execStyle('italic')">I</button>
                    <div class="color-wrapper" title="Couleur de sélection">
                        <input type="color" oninput="changeTextColor(this.value)">
                    </div>
                </div>

                <div class="row-align">
                    <button class="tool-btn" onclick="execStyle('justifyLeft')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 10H3M21 6H3M21 14H3M17 18H3"/></svg></button>
                    <button class="tool-btn" onclick="execStyle('justifyCenter')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10H6M21 6H3M21 14H3M18 18H6"/></svg></button>
                    <button class="tool-btn" onclick="execStyle('justifyRight')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10H7M21 6H3M21 14H3M21 18H7"/></svg></button>
                    <button class="tool-btn" onclick="execStyle('justifyFull')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10H3M21 6H3M21 14H3M21 18H3"/></svg></button>
                </div>
            </div>

            <span class="section-label">DISPOSITION (FLOAT)</span>
            <div class="row-float">
                <button class="tool-btn" onclick="addFloatBlock('left')" title="Image Gauche">
                    <svg class="float-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" fill="currentColor" fill-opacity="0.2"/><path d="M14 4h7M14 8h7M3 14h18M3 18h18"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('full')" title="Image Large">
                    <svg class="float-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="8" fill="currentColor" fill-opacity="0.2"/><path d="M3 14h18M3 18h18"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('right')" title="Image Droite">
                    <svg class="float-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="13" y="3" width="8" height="8" fill="currentColor" fill-opacity="0.2"/><path d="M3 4h7M3 8h7M3 14h18M3 18h18"/></svg>
                </button>
            </div>
            <div class="gauge-row">
                <div class="gauge-info"><span>LARGEUR IMAGE</span><span class="gauge-data"><span id="val-img-width">40</span>%</span></div>
                <input type="range" id="slider-img-width" style="width:100%; accent-color:#fff;" min="10" max="100" value="40" oninput="updateImageWidth(this.value)">
            </div>

            <span class="section-label">STRUCTURE (GRILLE)</span>
            <div class="row-h" style="margin-bottom:8px;">
                <button class="tool-btn" onclick="addGridBlock(2)">COL 2</button>
                <button class="tool-btn" onclick="addGridBlock(3)">COL 3</button>
                <button class="tool-btn" onclick="addGridBlock(4)">COL 4</button>
            </div>
            <div class="gauge-row">
                <div class="gauge-info"><span>ESPACEMENT (GUTTER)</span><span class="gauge-data"><span id="val-gutter">20</span>px</span></div>
                <input type="range" id="slider-gutter" style="width:100%; accent-color:#fff;" min="0" max="100" value="20" oninput="updateGutter(this.value)">
            </div>

            <span class="section-label">RÉGLAGES : <span id="target-label" style="color:#fff">H1</span></span>
            <div class="gauge-row">
                <div class="gauge-info"><span>TAILLE</span><span class="gauge-data"><span id="val-size">64</span>px</span></div>
                <input type="range" id="slider-size" style="width:100%; accent-color:#fff;" min="8" max="120" value="64" oninput="updateStyle('fontSize', this.value+'px', 'val-size')">
            </div>
        </div>

        <div class="sidebar-footer">
            <button onclick="publishProject()" style="background:#fff; color:#000; border:none; padding:15px; font-weight:900; cursor:pointer; text-transform:uppercase;">PUBLIER</button>
            <a href="../index.php" style="color:var(--sidebar-muted); text-align:center; font-size:10px; text-decoration:none; border:1px solid var(--sidebar-border); padding:10px; border-radius:4px;">RETOUR</a>
        </div>
    </aside>

    <main class="canvas">
        <article class="paper" id="paper">
            <div class="block-container">
                <div class="delete-block" onclick="this.parentElement.remove()">✕</div>
                <h1 id="editable-main-title" contenteditable="true" onfocus="setTarget('h1')"><?php echo htmlspecialchars($title); ?></h1>
            </div>
            <div id="editor-core"><?php echo $htmlContent; ?></div>
        </article>
    </main>

    <script>
    let currentTag = 'h1';
    let currentImageElement = null;
    let designSystem = { 'h1': { fontSize: '64px' }, 'h2': { fontSize: '42px' }, 'h3': { fontSize: '30px' }, 'h4': { fontSize: '24px' }, 'h5': { fontSize: '18px' }, 'p':  { fontSize: '18px' } };
    let currentGutter = '20px';

    const LOREM = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.";

    function renderStyles() {
        let css = "";
        for (let tag in designSystem) {
            css += `.paper ${tag} { font-size: ${designSystem[tag].fontSize}; margin-top:0; margin-bottom:0.5em; outline:none; }\n`;
        }
        css += `.grid-block { gap: ${currentGutter}; }`;
        document.getElementById('dynamic-styles').innerHTML = css;
    }

    // --- LOGIQUE DE PUBLICATION ---
    function publishProject() {
        const payload = {
            slug: document.getElementById('inp-slug').value,
            title: document.getElementById('editable-main-title').innerText,
            summary: document.getElementById('inp-summary').value,
            category: "Design",
            designSystem: designSystem,
            htmlContent: document.getElementById('editor-core').innerHTML
        };

        fetch('save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(res => {
            alert(res.message);
            if(res.status === 'success') {
                window.location.href = '../index.php';
            }
        })
        .catch(err => {
            console.error("Erreur de publication:", err);
            alert("Erreur technique lors de la publication.");
        });
    }

    function updateStyle(prop, val, displayId) {
        designSystem[currentTag][prop] = val;
        document.getElementById(displayId).innerText = val.replace('px', '');
        renderStyles();
    }

    function updateGutter(val) {
        currentGutter = val + 'px';
        document.getElementById('val-gutter').innerText = val;
        renderStyles();
    }

    function updateImageWidth(val) {
        if(currentImageElement) {
            currentImageElement.style.width = val + '%';
            document.getElementById('val-img-width').innerText = val;
        }
    }

    function setTarget(tag, imgEl = null) {
        currentTag = tag;
        currentImageElement = imgEl;
        document.getElementById('target-label').innerText = tag.toUpperCase() === 'P' ? 'PARAGRAPHE' : tag.toUpperCase();
        
        if(designSystem[tag]) {
            let val = parseInt(designSystem[tag].fontSize);
            document.getElementById('slider-size').value = val;
            document.getElementById('val-size').innerText = val;
        }

        if(imgEl) {
            let w = parseInt(imgEl.style.width) || 40;
            document.getElementById('slider-img-width').value = w;
            document.getElementById('val-img-width').innerText = w;
        }
    }

    function changeTextColor(color) { document.execCommand('foreColor', false, color); }
    function toggleTheme() {
        document.body.classList.toggle('light-mode');
        document.getElementById('t-icon').innerText = document.body.classList.contains('light-mode') ? '☀️' : '🌙';
    }
    function toggleSidebar() { document.body.classList.toggle('sidebar-hidden'); }
    function execStyle(cmd) { document.execCommand(cmd, false, null); }

    function addBlock(tag, txt = null) {
        const content = (tag === 'p' && !txt) ? LOREM : (txt || 'Nouveau texte');
        const container = document.createElement('div');
        container.className = 'block-container';
        container.innerHTML = `<div class="delete-block" onclick="this.parentElement.remove()">✕</div><${tag} contenteditable="true" onfocus="setTarget('${tag}')">${content}</${tag}>`;
        document.getElementById('editor-core').appendChild(container);
        container.querySelector(tag).focus();
    }

    function addFloatBlock(type) {
        const container = document.createElement('div');
        container.className = 'block-container';
        let style = "";
        let width = (type === 'full') ? "100%" : "40%";
        if(type === 'left') style = `float:left; margin:0 20px 10px 0; width:${width}; aspect-ratio:16/9;`;
        if(type === 'right') style = `float:right; margin:0 0 10px 20px; width:${width}; aspect-ratio:16/9;`;
        if(type === 'full') style = `width:${width}; margin-bottom:20px; aspect-ratio:21/9;`;

        container.innerHTML = `
            <div class="delete-block" onclick="this.parentElement.remove()">✕</div>
            <div class="float-block">
                <div class="img-placeholder" onclick="setTarget('img', this)" style="${style} background:#f0f0f0; border:1px solid #ddd; display:flex; align-items:center; justify-content:center; color:#999; font-size:10px; cursor:pointer;">IMAGE</div>
                <p contenteditable="true" onfocus="setTarget('p')">${LOREM}</p>
            </div>`;
        document.getElementById('editor-core').appendChild(container);
    }

    function addGridBlock(cols) {
        const container = document.createElement('div');
        container.className = 'block-container';
        let items = "";
        for(let i=0; i<cols; i++) {
            items += `<div class="grid-item"><p contenteditable="true" onfocus="setTarget('p')">${LOREM}</p></div>`;
        }
        container.innerHTML = `
            <div class="delete-block" onclick="this.parentElement.remove()">✕</div>
            <div class="grid-block" style="grid-template-columns: repeat(${cols}, minmax(0, 1fr));">
                ${items}
            </div>`;
        document.getElementById('editor-core').appendChild(container);
    }

    window.onload = () => { renderStyles(); setTarget('h1'); };
    </script>
</body>
</html>