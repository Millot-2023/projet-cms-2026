<?php
/**
 * PROJET-CMS-2026 - ÉDITEUR (SIDEBAR ZERO-TRACE)
 * @author: Christophe Millot
 */

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { die("Acces reserve."); exit; }

$content_dir = "../content/";
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$title = "Titre du Projet";

if ($slug && file_exists($content_dir . $slug . '/data.php')) {
    include $content_dir . $slug . '/data.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditeur - CMS 2026</title>
    <style>
        :root { 
            --sidebar-bg: #111; 
            --accent: #fff; 
            --text-muted: #666; 
            --red-close: #ff4d4d; 
            --canvas-bg: #e5e5e5;
        }
        
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: #222; 
            color: #fff; 
            display: flex; 
            height: 100vh; 
            overflow: hidden; 
            width: 100vw; 
        }
        
        /* SIDEBAR */
        .sidebar { 
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 320px; background: var(--sidebar-bg); padding: 30px 20px; 
            display: flex; flex-direction: column; border-right: 1px solid #333; 
            z-index: 100; 
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
            transform: translateX(0);
        }
        body.sidebar-hidden .sidebar { transform: translateX(-100%); }

        .sidebar h2 { 
            font-size: 14px; letter-spacing: 2px; text-transform: uppercase; 
            margin-bottom: 40px; display: flex; align-items: center; color: #fff;
        }

        .close-sidebar { color: var(--red-close); cursor: pointer; font-weight: bold; font-size: 18px; margin-right: 15px; }
        .mode-toggle { margin-left: auto; cursor: pointer; font-size: 18px; }

        .section-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; margin-top: 20px; margin-bottom: 15px; display: block; }
        
        .btn-main { 
            width: 100%; background: #fff; color: #000; border: none; padding: 12px; 
            font-weight: bold; cursor: pointer; margin-bottom: 10px; text-transform: uppercase; font-size: 11px; 
        }

        .grid-tools { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 10px; }
        .tool-btn { 
            background: #222; border: 1px solid #333; color: #fff; height: 40px; 
            cursor: pointer; display: flex; align-items: center; justify-content: center; 
        }
        .tool-btn:hover { background: #fff; color: #000; }

        /* --- LE BLOC DES JAUGES EN FLEX --- */
        .gauge-row {
            display: flex;
            align-items: center;
            height: 35px;
            gap: 10px;
            margin-bottom: 5px;
        }
        .gauge-label {
            width: 70px;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        .gauge-control {
            flex: 1;
            display: flex;
            align-items: center;
        }
        .gauge-slider {
            width: 100%;
            cursor: pointer;
        }
        .gauge-data {
            width: 50px;
            text-align: right;
            font-family: monospace;
            font-size: 11px;
            color: #fff;
        }
        /* ---------------------------------- */

        .publish-btn { 
            margin-top: auto; 
            background: #fff; color: #000; border: none; padding: 15px; 
            cursor: pointer; font-weight: bold; text-transform: uppercase; font-size: 12px;
        }

        .exit-btn { 
            margin-top: 20px; 
            background: transparent; border: 1px solid #333; color: #888; padding: 12px; 
            cursor: pointer; font-weight: 300; text-transform: uppercase; font-size: 11px;
            transition: all 0.3s;
        }
        .exit-btn:hover { color: #fff; border-color: #666; }

        .canvas { 
            flex-grow: 1; width: 100vw; background: var(--canvas-bg); 
            display: flex; justify-content: center; padding: 60px; 
            overflow-y: auto; transition: padding-left 0.4s;
        }
        body:not(.sidebar-hidden) .canvas { padding-left: 380px; } 
        
        .paper { 
            width: 850px; background: #fff; color: #000; min-height: 1100px; padding: 100px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.1); transition: background 0.4s, color 0.4s; 
        }
        .paper.dark-mode { background: #000 !important; color: #fff !important; }
        .paper h1 { font-size: 48px; text-align: center; margin-bottom: 60px; outline: none; }
        .content-area { font-size: 20px; line-height: 1.6; outline: none; min-height: 600px; }

        .settings-trigger {
            position: fixed; top: 20px; left: 20px; width: 45px; height: 45px;
            background: #000; color: #fff; border: 1px solid #333;
            display: none; align-items: center; justify-content: center;
            cursor: pointer; z-index: 50; border-radius: 4px;
        }
        body.sidebar-hidden .settings-trigger { display: flex; }
    </style>
</head>
<body>

    <div class="settings-trigger" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="white"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 00.12-.61l-1.92-3.32a.488.488 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 00-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32a.49.49 0 00-.12-.61l-2.03-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
    </div>

    <aside class="sidebar" id="sidebar">
        <h2>
            <span class="close-sidebar" onclick="toggleSidebar()">✕</span>
            PARAMÈTRES 
            <span id="icon-toggle" class="mode-toggle" onclick="toggleDarkMode()">🌙</span>
        </h2>

        <span class="section-label">TYPOGRAPHIE</span>
        <button class="btn-main" onclick="addManifeste()">AJOUTER MANIFESTE</button>
        
        <div class="grid-tools">
            <button class="tool-btn" onmousedown="event.preventDefault(); document.execCommand('justifyLeft');">L</button>
            <button class="tool-btn" onmousedown="event.preventDefault(); document.execCommand('justifyCenter');">C</button>
            <button class="tool-btn" onmousedown="event.preventDefault(); document.execCommand('justifyRight');">R</button>
            <button class="tool-btn" onmousedown="event.preventDefault(); document.execCommand('justifyFull');">F</button>
        </div>

        <span class="section-label">ARCHITECTURE</span>
        <div class="grid-tools">
            <button class="tool-btn" onclick="grid(1)">H1</button>
            <button class="tool-btn" onclick="grid(2)">H2</button>
            <button class="tool-btn" onclick="grid(3)">H3</button>
            <button class="tool-btn" onclick="grid(4)">H4</button>
        </div>

        <span class="section-label">PILOTAGE TYPO</span>

        <div class="gauge-row">
            <div class="gauge-label">Taille</div>
            <div class="gauge-control">
                <input type="range" class="gauge-slider" min="12" max="100" value="48" oninput="document.getElementById('editable-title').style.fontSize = this.value + 'px'; document.getElementById('val-size').innerText = this.value;">
            </div>
            <div class="gauge-data"><span id="val-size">48</span>px</div>
        </div>

        <div class="gauge-row">
            <div class="gauge-label">Graisse</div>
            <div class="gauge-control">
                <input type="range" class="gauge-slider" min="100" max="900" step="100" value="700" oninput="document.getElementById('editable-title').style.fontWeight = this.value; document.getElementById('val-weight').innerText = this.value;">
            </div>
            <div class="gauge-data"><span id="val-weight">700</span></div>
        </div>

        <div class="gauge-row">
            <div class="gauge-label">Hauteur</div>
            <div class="gauge-control">
                <input type="range" class="gauge-slider" min="0.8" max="2.5" step="0.1" value="1.2" oninput="document.getElementById('editable-title').style.lineHeight = this.value; document.getElementById('val-height').innerText = this.value;">
            </div>
            <div class="gauge-data"><span id="val-height">1.2</span></div>
        </div>

        <button class="publish-btn" onclick="saveData()">PUBLIER</button>
        <button class="exit-btn" onclick="window.location.href='../index.php'">Quitter l'éditeur</button>
    </aside>

    <main class="canvas">
        <article class="paper" id="paper">
            <h1 contenteditable="true" id="editable-title"><?php echo htmlspecialchars($title); ?></h1>
            <div class="content-area" contenteditable="true" id="editor-core">
                Focus sur l'écriture...
            </div>
        </article>
    </main>

    <script>
    function toggleSidebar() { document.body.classList.toggle('sidebar-hidden'); }

    function toggleDarkMode() {
        const paper = document.getElementById('paper');
        const icon = document.getElementById('icon-toggle');
        paper.classList.toggle('dark-mode');
        icon.innerText = paper.classList.contains('dark-mode') ? '☀️' : '🌙';
    }

    function addManifeste() {
        const editor = document.getElementById('editor-core');
        const div = document.createElement('div');
        div.innerText = "Nouveau bloc...";
        editor.appendChild(div);
        div.focus();
    }

    function grid(cols) { document.getElementById('editor-core').style.columnCount = cols; }
    function saveData() { alert('Données sauvegardées (simulation)'); }
    </script>
</body>
</html>