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
        :root { 
            --sidebar-bg: #111; 
            --accent: #fff; 
            --text-muted: #666; 
            --canvas-bg: #1a1a1a; 
        }
        
        body { margin: 0; font-family: 'Inter', sans-serif; background: #000; color: #fff; display: flex; height: 100vh; overflow: hidden; width: 100vw; }
        
        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 340px; background: var(--sidebar-bg); display: flex; flex-direction: column; border-right: 1px solid #333; z-index: 100; transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1); }
        body.sidebar-hidden .sidebar { transform: translateX(-100%); }
        
        .sidebar-header { padding: 40px 25px 25px; border-bottom: 1px solid #222; flex-shrink: 0; }
        .sidebar-header h2 { font-size: 12px; letter-spacing: 3px; text-transform: uppercase; margin: 0; color: var(--text-muted); }
        
        .sidebar-scroll { flex-grow: 1; overflow-y: auto; padding: 20px 25px; scrollbar-width: thin; scrollbar-color: #333 transparent; }
        
        .sidebar-footer { padding: 25px; border-top: 1px solid #222; background: #0a0a0a; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px; }
        
        .section-label { font-size: 9px; color: var(--text-muted); text-transform: uppercase; margin-top: 30px; margin-bottom: 12px; display: block; letter-spacing: 1px; }
        .admin-input { width: 100%; background: #1a1a1a; border: 1px solid #333; color: #fff; padding: 12px; margin-bottom: 12px; font-size: 11px; box-sizing: border-box; border-radius: 4px; }
        
        .grid-targets { display: grid; gap: 8px; margin-bottom: 15px; grid-template-columns: repeat(3, 1fr); }
        .grid-style { display: grid; gap: 8px; margin-bottom: 15px; grid-template-columns: repeat(2, 1fr); }
        
        .tool-btn { background: #1a1a1a; border: 1px solid #333; color: #666; height: 45px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; border-radius: 4px; transition: all 0.2s; text-decoration: none; }
        .tool-btn:hover { border-color: #555; color: #999; }
        .tool-btn.active { background: #fff; color: #000; border-color: #fff; font-weight: bold; }
        
        .gauge-row { background: #1a1a1a; padding: 15px; border-radius: 6px; margin-bottom: 10px; border: 1px solid #222; }
        .gauge-info { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .gauge-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; }
        .gauge-data { font-family: monospace; font-size: 11px; color: #fff; }
        .gauge-slider { width: 100%; cursor: pointer; accent-color: #fff; height: 4px; }
        
        .canvas { flex-grow: 1; height: 100vh; background: var(--canvas-bg); display: flex; flex-direction: column; align-items: center; padding: 80px 20px; overflow-y: scroll; transition: padding-left 0.4s; }
        body:not(.sidebar-hidden) .canvas { padding-left: 360px; }
        
        .paper { position: relative; width: 100%; max-width: 850px; background: #fff; color: #000; min-height: 1200px; height: auto; padding: 120px 100px; box-shadow: 0 40px 100px rgba(0,0,0,0.5); display: block; overflow-wrap: break-word; flex-shrink: 0; margin-bottom: 100px; }
        
        .block-container { position: relative; width: 100%; margin-bottom: 5px; padding: 2px 0; }
        
        .delete-btn { 
            position: absolute; 
            left: -40px; 
            top: 50%; 
            transform: translateY(-50%);
            width: 30px; 
            height: 30px; 
            background: #000; 
            color: #fff; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            opacity: 0; 
            transition: opacity 0.2s, background 0.2s;
            z-index: 10;
        }

        .delete-btn::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 50px;
            height: 100%;
            background: transparent;
            z-index: -1;
        }

        .block-container:hover .delete-btn { opacity: 1; }
        .delete-btn:hover { background: #ff4d4d; }
        
        [contenteditable] { outline: none; }
        
        .publish-btn { width: 100%; background: #fff; color: #000; border: none; padding: 18px; cursor: pointer; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; font-size: 11px; border-radius: 4px; }
        .exit-btn { width: 100%; background: transparent; color: #444; border: 1px solid #222; padding: 12px; cursor: pointer; font-weight: 400; text-transform: uppercase; letter-spacing: 1px; font-size: 10px; border-radius: 4px; text-decoration: none; text-align: center; transition: all 0.2s; }
        .exit-btn:hover { border-color: #444; color: #888; }

        .sidebar-trigger { position: fixed; top: 20px; left: 20px; z-index: 50; cursor: pointer; background: #fff; color: #000; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 4px; font-weight: bold; }
        img { max-width: 100%; height: auto; display: block; margin: 20px 0; border-radius: 2px; }
    </style>
</head>
<body>

    <div class="sidebar-trigger" onclick="toggleSidebar()">☰</div>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>PROJET STUDIO <span onclick="toggleSidebar()" style="cursor:pointer; float:right;">✕</span></h2>
        </div>

        <div class="sidebar-scroll">
            <span class="section-label">MÉTADONNÉES</span>
            <input type="text" id="inp-slug" class="admin-input" placeholder="slug" value="<?php echo $slug; ?>">
            <input type="text" id="inp-cat" class="admin-input" placeholder="Catégorie" value="<?php echo $category; ?>">
            <textarea id="inp-summary" class="admin-input" style="height:60px;" placeholder="Résumé"><?php echo $summary; ?></textarea>

            <span class="section-label">STRUCTURE</span>
            <div class="grid-targets">
                <button class="tool-btn" id="btn-p" onclick="addBlock('p', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.')">P</button>
                <button class="tool-btn" id="btn-h2" onclick="addBlock('h2', 'Titre de section')">H2</button>
                <button class="tool-btn" id="btn-h3" onclick="addBlock('h3', 'Sous-titre H3')">H3</button>
            </div>
            <div class="grid-style" style="grid-template-columns: repeat(2, 1fr);">
                <button class="tool-btn" id="btn-h4" onclick="addBlock('h4', 'Titre H4')">H4</button>
                <button class="tool-btn" id="btn-h5" onclick="addBlock('h5', 'Titre H5')">H5</button>
            </div>

            <span class="section-label">ENRICHISSEMENT</span>
            <div class="grid-style">
                <button class="tool-btn" onclick="execStyle('bold')" style="font-weight: bold;">B</button>
                <button class="tool-btn" onclick="execStyle('italic')" style="font-style: italic;">I</button>
            </div>
            <button class="tool-btn" onclick="addImage()" style="width:100%; margin-bottom:20px;">+ AJOUTER IMAGE</button>

            <div id="gauge-controls">
                <span class="section-label">RÉGLAGES : <span id="target-label" style="color:#fff">...</span></span>
                <div class="gauge-row">
                    <div class="gauge-info"><span class="gauge-label">Taille</span><span class="gauge-data"><span id="val-size">--</span>px</span></div>
                    <input type="range" id="slider-size" class="gauge-slider" min="8" max="160" value="18" oninput="updateGlobalStyle('fontSize', this.value + 'px', 'val-size')">
                </div>

                <div class="gauge-row">
                    <div class="gauge-info"><span class="gauge-label">Interlignage</span><span class="gauge-data"><span id="val-lh">--</span></span></div>
                    <input type="range" id="slider-lh" class="gauge-slider" min="0.7" max="2.5" step="0.1" value="1.6" oninput="updateGlobalStyle('lineHeight', this.value, 'val-lh')">
                </div>

                <div class="grid-targets" style="grid-template-columns: repeat(4, 1fr);">
                    <button class="tool-btn" onclick="updateGlobalStyle('textAlign', 'left')">L</button>
                    <button class="tool-btn" onclick="updateGlobalStyle('textAlign', 'center')">C</button>
                    <button class="tool-btn" onclick="updateGlobalStyle('textAlign', 'right')">R</button>
                    <button class="tool-btn" onclick="updateGlobalStyle('textAlign', 'justify')">J</button>
                </div>
            </div>
        </div>

        <div class="sidebar-footer">
            <button class="publish-btn" onclick="publishDesign()">PUBLIER LE PROJET</button>
            <a href="../" class="exit-btn">Quitter l'éditeur</a>
        </div>
    </aside>

    <main class="canvas">
        <article class="paper" id="paper">
            <h1 contenteditable="true" id="editable-h1" onfocus="setGlobalTarget('h1')"><?php echo htmlspecialchars($title); ?></h1>
            <div class="content-area" id="editor-core"></div>
        </article>
    </main>

    <script>
    let currentTag = 'h1';
    let designSystem = <?php echo isset($designSystem) ? json_encode($designSystem) : json_encode([
        'p'  => ['fontSize' => '18px', 'lineHeight' => '1.6', 'textAlign' => 'left'],
        'h1' => ['fontSize' => '64px', 'lineHeight' => '1.1', 'textAlign' => 'center'],
        'h2' => ['fontSize' => '42px', 'lineHeight' => '1.2', 'textAlign' => 'left'],
        'h3' => ['fontSize' => '30px', 'lineHeight' => '1.3', 'textAlign' => 'left'],
        'h4' => ['fontSize' => '22px', 'lineHeight' => '1.4', 'textAlign' => 'left'],
        'h5' => ['fontSize' => '18px', 'lineHeight' => '1.5', 'textAlign' => 'left']
    ]); ?>;

    function toggleSidebar() { document.body.classList.toggle('sidebar-hidden'); }
    function execStyle(cmd) { document.execCommand(cmd, false, null); }

    function setGlobalTarget(tag) {
        currentTag = tag;
        document.getElementById('target-label').innerText = tag.toUpperCase();
        document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
        const btn = document.getElementById('btn-' + tag);
        if(btn) btn.classList.add('active');
        
        if(designSystem[tag]) {
            const fs = parseInt(designSystem[tag].fontSize);
            const lh = designSystem[tag].lineHeight;
            document.getElementById('slider-size').value = fs;
            document.getElementById('val-size').innerText = fs;
            document.getElementById('slider-lh').value = lh;
            document.getElementById('val-lh').innerText = lh;
        }
    }

    function updateGlobalStyle(prop, value, displayId) {
        if(!designSystem[currentTag]) designSystem[currentTag] = {};
        designSystem[currentTag][prop] = value;
        if(displayId) document.getElementById(displayId).innerText = value.replace('px', '');
        renderStyles();
    }

    function renderStyles() {
        let css = "";
        for (const tag in designSystem) {
            css += `.paper ${tag} { font-size: ${designSystem[tag].fontSize}; line-height: ${designSystem[tag].lineHeight}; text-align: ${designSystem[tag].textAlign}; font-weight: inherit; }\n`;
        }
        document.getElementById('dynamic-styles').innerHTML = css;
    }

    function addBlock(tag, content) {
        const core = document.getElementById('editor-core');
        const container = document.createElement('div');
        container.className = "block-container";
        const newEl = document.createElement(tag);
        newEl.contentEditable = "true";
        newEl.innerHTML = content;
        newEl.onfocus = () => setGlobalTarget(tag);
        
        newEl.onpaste = (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand("insertHTML", false, text);
        };

        const delBtn = document.createElement('div');
        delBtn.className = "delete-btn";
        delBtn.innerHTML = "✕";
        delBtn.onclick = () => container.remove();
        
        container.appendChild(newEl);
        container.appendChild(delBtn);
        core.appendChild(container);
        newEl.focus();
    }

    function addImage() {
        const url = prompt("URL image :");
        if (url) {
            const container = document.createElement('div');
            container.className = "block-container";
            const img = document.createElement('img');
            img.src = url;
            const delBtn = document.createElement('div');
            delBtn.className = "delete-btn";
            delBtn.innerHTML = "✕";
            delBtn.onclick = () => container.remove();
            container.appendChild(img);
            container.appendChild(delBtn);
            document.getElementById('editor-core').appendChild(container);
        }
    }

    async function publishDesign() {
        const coreClone = document.getElementById('editor-core').cloneNode(true);
        coreClone.querySelectorAll('.delete-btn').forEach(el => el.remove());
        coreClone.querySelectorAll('[contenteditable]').forEach(el => el.removeAttribute('contenteditable'));
        
        const data = {
            slug: document.getElementById('inp-slug').value,
            category: document.getElementById('inp-cat').value,
            summary: document.getElementById('inp-summary').value,
            title: document.getElementById('editable-h1').innerText,
            designSystem: designSystem,
            htmlContent: coreClone.innerHTML
        };

        const response = await fetch('save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        alert(result.message);
    }

    window.onload = () => { 
        renderStyles(); 
        setGlobalTarget('h1'); 
        const rawContent = "<?php echo $safeHtml; ?>";
        if(rawContent.trim() !== "") {
            const temp = document.createElement('div');
            temp.innerHTML = rawContent;
            Array.from(temp.children).forEach(child => {
                addBlock(child.tagName.toLowerCase(), child.innerHTML);
            });
        }
    };
    </script>
</body>
</html>