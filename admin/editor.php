<?php
/**
 * PROJET-CMS-2026 - ÉDITEUR DESIGN SYSTEM (VERSION v3.0.4-FIX)
 * @author: Christophe Millot
 */

// 1. Chargement de la configuration centrale
require_once '../core/config.php';

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { die("Acces reserve."); exit; }

$content_dir = "../content/";
$trash_dir   = "../content/_trash/";

// --- LOGIQUE DE GESTION DE LA CORBEILLE ---
if (isset($_GET['action']) && isset($_GET['slug'])) {
    $action = $_GET['action'];
    $slug   = $_GET['slug'];

    if ($action === 'restore') {
        $parts = explode('_', $slug, 2);
        $original_name = isset($parts[1]) ? $parts[1] : $slug;
        if (rename($trash_dir . $slug, $content_dir . $original_name)) {
            header('Location: ' . BASE_URL . 'index.php?status=restored');
            exit;
        }
    }

    if ($action === 'purge') {
        $target = $trash_dir . $slug;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if (rmdir($target)) {
            header('Location: trash.php?status=purged');
            exit;
        }
    }
}

$slug = isset($_GET['project']) ? $_GET['project'] : '';
if (empty($slug)) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Valeurs par défaut
$title = "Titre du Projet";
$category = "Design";
$summary = "";
$cover = ""; 
$date = date("d/m/Y");
$htmlContent = "";
$designSystemArray = [ 
    'h1' => [ 'fontSize' => '64px' ], 
    'h2' => [ 'fontSize' => '42px' ], 
    'h3' => [ 'fontSize' => '30px' ], 
    'h4' => [ 'fontSize' => '24px' ], 
    'h5' => [ 'fontSize' => '18px' ], 
    'p' =>  [ 'fontSize' => '18px' ] 
];

// --- CHARGEMENT HYBRIDE ---
$is_in_trash = false;
$current_project_dir = $content_dir . $slug . '/';

if (!file_exists($current_project_dir)) {
    $current_project_dir = $trash_dir . $slug . '/';
    $is_in_trash = true;
}

$data_path = $current_project_dir . 'data.php';
if (file_exists($data_path)) {
    $data_loaded = include $data_path;
    
    if (is_array($data_loaded)) {
        $title = $data_loaded['title'] ?? $title;
        $category = $data_loaded['category'] ?? $category;
        $summary = $data_loaded['summary'] ?? $summary;
        $cover = $data_loaded['cover'] ?? $cover;
        $date = $data_loaded['date'] ?? $date;
        $htmlContent = $data_loaded['htmlContent'] ?? $htmlContent;
        $designSystemArray = $data_loaded['designSystem'] ?? $designSystemArray;
    }
}

$cover_path = "";
if (!empty($cover)) {
    // Correction du chemin pour gérer le dossier corbeille si nécessaire
    $sub_folder = $is_in_trash ? 'content/_trash/' : 'content/';
    $cover_path = (strpos($cover, 'data:image') === 0) ? $cover : BASE_URL . $sub_folder . $slug . '/' . $cover;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ÉDITEUR - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap">
    <style id="dynamic-styles"></style>
    <link rel="stylesheet" href="../assets/css/main.css">
<style>


</style>









</head>
<body class="dark-mode"> 
    <button class="sidebar-trigger" onclick="toggleSidebar()">☰</button>

    <aside class="sidebar">
        <div class="sidebar-header">
            <span style="color:#ff4d4d; cursor:pointer; font-weight:bold;" onclick="toggleSidebar()">✕</span>
            <h2><?php echo SITE_NAME; ?></h2>
            <div class="theme-toggle" onclick="toggleTheme()" id="t-icon" style="cursor:pointer">🌙</div>
        </div>

        <div class="sidebar-scroll">
            <span class="section-label">APERÇU CARTE</span>
            <div class="preview-card-container" id="preview-container">
                <?php if(!empty($cover_path)): ?>
                    <img src="<?php echo $cover_path; ?>" id="img-cover-preview" class="<?php echo $is_in_trash ? 'img-trash' : ''; ?>">
                <?php else: ?>
                    <span style="font-size:8px; color:#444;">AUCUNE IMAGE</span>
                <?php endif; ?>
            </div>
            <button class="tool-btn" style="height:30px; font-size:9px;" onclick="document.getElementById('inp-cover').click()">Changer l'image</button>
            <input type="file" id="inp-cover" style="display:none;" onchange="handleCoverChange(this)">

            <span class="section-label">MÉTADONNÉES</span>
            <input type="text" id="inp-slug" class="admin-input" value="<?php echo htmlspecialchars($slug); ?>" readonly>
            <input type="text" id="inp-date" class="admin-input" value="<?php echo htmlspecialchars($date); ?>" readonly>
            <textarea id="inp-summary" class="admin-input" placeholder="Résumé" style="height:60px;"><?php echo htmlspecialchars($summary); ?></textarea>

            <span class="section-label">TYPOGRAPHIE</span>
            <div class="row-h">
                <button class="tool-btn" onclick="addBlock('h1', 'Titre H1')">H1</button>
                <button class="tool-btn" onclick="addBlock('h2', 'Titre H2')">H2</button>
                <button class="tool-btn" onclick="addBlock('h3', 'Titre H3')">H3</button>
                <button class="tool-btn" onclick="addBlock('h4', 'Titre H4')">H4</button>
                <button class="tool-btn" onclick="addBlock('h5', 'Titre H5')">H5</button>
            </div>
            <button class="tool-btn" onclick="addBlock('p')" style="margin-top:8px;">Paragraphe</button>
            <div class="row-styles" style="margin-top:8px;">
                <button class="tool-btn" onclick="execStyle('bold')">B</button>
                <button class="tool-btn" onclick="execStyle('italic')">I</button>
                <div class="color-wrapper" style="position: relative; width: 100%; height: 40px; border: 1px solid var(--sidebar-border); border-radius: 4px; overflow: hidden; background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);"><input type="color" oninput="changeTextColor(this.value)" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"></div>
            </div>

            <span class="section-label">RÉGLAGES : <span id="target-label" style="color:#fff">H1</span></span>
            <div class="gauge-row">
                <div class="gauge-info"><span>TAILLE POLICE</span><span id="val-size">64</span>px</div>
                <input type="range" id="slider-size" min="8" max="120" value="64" oninput="updateStyle('fontSize', this.value+'px', 'val-size')">
            </div>


            <span class="section-label">DISPOSITION (FLOAT)</span>










<div class="row-float">
    <button class="tool-btn" onclick="addFloatBlock('left')" title="Aligner à gauche">
        <div class="ico-align ico-left"></div>
    </button>
    <button class="tool-btn" onclick="addFloatBlock('full')" title="Pleine largeur">
        <div class="ico-align ico-full"></div>
    </button>
    <button class="tool-btn" onclick="addFloatBlock('right')" title="Aligner à droite">
        <div class="ico-align ico-right"></div>
    </button>
</div>







            <div class="gauge-row">
                <div class="gauge-info"><span>IMAGE WIDTH</span><span id="val-img-width">40</span>%</div>
                <input type="range" id="slider-img-width" min="10" max="100" value="40" oninput="updateImageWidth(this.value)">
            </div>
        </div>

        <div class="sidebar-footer">
            <button onclick="exportForGmail()" class="btn-gmail">✉️ EXPORT GMAIL</button>
            <button id="btn-publish-trigger" onclick="publishProject()" class="btn-publish">PUBLIER</button>
            <a href="<?php echo BASE_URL; ?>index.php" class="btn-exit">QUITTER</a>
        </div>
    </aside>

    <main class="canvas">
        <div class="responsive-switcher">
            <button class="tool-btn" style="width: 100px;" onclick="resizePaper('100%')">DESKTOP</button>
            <button class="tool-btn" style="width: 100px;" onclick="resizePaper('768px')">TABLETTE</button>
            <button class="tool-btn" style="width: 100px;" onclick="resizePaper('375px')">MOBILE</button>
        </div>
        <article class="paper" id="paper">
            <div class="block-container">
                <div class="delete-block" onclick="this.parentElement.remove()">✕</div>
                <h1 id="main-title" contenteditable="true" onfocus="setTarget('h1')"><?php echo htmlspecialchars($title); ?></h1>
            </div>
            <div id="editor-core"><?php echo $htmlContent; ?></div>
        </article>
    </main>

    <script>
    var coverData = "<?php echo $cover; ?>"; 
    var currentTag = 'h1';
    var currentImageElement = null;
    var designSystem = <?php echo json_encode($designSystemArray); ?>;
    var LOREM_TEXT = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

    function renderStyles() {
        var dynStyle = document.getElementById('dynamic-styles');
        if(!dynStyle) return;
        var css = ".paper { display: flow-root; padding-top: 40px !important; }\n"; 
        for (var tag in designSystem) {
            var isHeading = tag.startsWith('h');
            var marginBottom = isHeading ? '0.5em' : '1.5em'; 
            var marginTop = isHeading ? '1.2em' : '0em';
            css += ".paper " + tag + ", #main-title { font-size: " + designSystem[tag].fontSize + "; line-height: 1.1 !important; margin-top: " + (tag === 'h1' ? '0' : marginTop) + " !important; margin-bottom: " + marginBottom + " !important; outline: none; display: block; }\n";
        }
        dynStyle.innerHTML = css;
    }

    function resizePaper(width) {
        var paper = document.getElementById('paper');
        if(paper) {
            paper.style.width = width;
            paper.style.maxWidth = (width === '100%') ? "850px" : width;
        }
    }

    function toggleSidebar() { document.body.classList.toggle('sidebar-hidden'); }
    function toggleTheme() { document.body.classList.toggle('light-mode'); }

    function setTarget(tag, imgEl) {
        currentTag = tag;
        currentImageElement = imgEl || null;
        var label = document.getElementById('target-label');
        if(label) label.innerText = tag.toUpperCase();
        if(designSystem[tag]) {
            var val = parseInt(designSystem[tag].fontSize);
            var slider = document.getElementById('slider-size');
            var display = document.getElementById('val-size');
            if(slider) slider.value = val;
            if(display) display.innerText = val;
        }
    }

    function updateStyle(prop, val, displayId) {
        if(designSystem[currentTag]) {
            designSystem[currentTag][prop] = val; 
            var display = document.getElementById(displayId);
            if(display) display.innerText = val.replace('px', ''); 
            renderStyles();
        }
    }

    function updateImageWidth(val) {
        if(currentImageElement) {
            currentImageElement.style.width = val + '%';
            var display = document.getElementById('val-img-width');
            if(display) display.innerText = val;
        }
    }

    function addBlock(tag, txt) {
        txt = txt || LOREM_TEXT;
        var container = document.createElement('div');
        container.className = 'block-container';
        container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div><' + tag + ' contenteditable="true" onfocus="setTarget(\'' + tag + '\')">' + txt + '</' + tag + '>';
        var core = document.getElementById('editor-core');
        if(core) core.appendChild(container);
    }

    function addFloatBlock(type) {
        var container = document.createElement('div');
        container.className = 'block-container';
        var width = (type === 'full') ? "100%" : "40%";
        var style = (type === 'left') ? "float:left; margin:0 20px 10px 0; width:" + width + ";" : (type === 'right') ? "float:right; margin:0 0 10px 20px; width:" + width + ";" : "width:" + width + "; margin-bottom:20px; clear:both;";
        container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div><div class="image-placeholder" onclick="setTarget(\'img\', this); event.stopPropagation();" ondblclick="triggerUpload(this)" style="' + style + ' background:#eee; aspect-ratio:16/9; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; position:relative;">IMAGE <input type="file" style="display:none;" onchange="handleImageSelect(this)"></div><p contenteditable="true" onfocus="setTarget(\'p\')">' + LOREM_TEXT + '</p>';
        var core = document.getElementById('editor-core');
        if(core) core.appendChild(container);
    }

    function triggerUpload(el) { 
        var inp = el.querySelector('input');
        if(inp) inp.click(); 
    }

    function handleImageSelect(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var placeholder = input.parentElement;
                placeholder.innerHTML = '<img src="' + e.target.result + '" style="width:100%; height:100%; object-fit:cover;"><input type="file" style="display:none;" onchange="handleImageSelect(this)">';
                var img = placeholder.querySelector('img');
                img.onclick = function(ev) { ev.stopPropagation(); setTarget('img', placeholder); };
                img.ondblclick = function(ev) { ev.stopPropagation(); triggerUpload(placeholder); };
                setTarget('img', placeholder);
            };
            reader.readAsDataURL(file);
        }
    }

    function handleCoverChange(input) {
        var file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                coverData = e.target.result;
                document.getElementById('preview-container').innerHTML = `<img src="${coverData}">`;
            };
            reader.readAsDataURL(file);
        }
    }

    function publishProject() {
        try {
            var elSlug = document.getElementById('inp-slug');
            var elTitle = document.getElementById('main-title');
            var elSummary = document.getElementById('inp-summary');
            var elCore = document.getElementById('editor-core');

            var formData = new FormData();
            formData.append('slug', elSlug ? elSlug.value : "");
            formData.append('title', elTitle ? elTitle.innerText : "Sans titre");
            formData.append('summary', elSummary ? elSummary.value : "");
            formData.append('htmlContent', elCore ? elCore.innerHTML : "");
            formData.append('coverImage', coverData);
            formData.append('designSystem', JSON.stringify(designSystem));

            fetch('save.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === "success") {
                    alert(data.message);
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("ERREUR RÉSEAU");
            });
        } catch (e) {
            console.error(e);
        }
    }

    function exportForGmail() {
        var elTitle = document.getElementById('main-title');
        var elCore = document.getElementById('editor-core');
        if(!elCore) return;
        var titleText = elTitle ? elTitle.innerText : "Projet";
        var temp = document.createElement('div');
        temp.innerHTML = elCore.innerHTML;
        temp.querySelectorAll('.delete-block, input').forEach(function(x) { x.remove(); });
        var emailTemplate = '<div style="padding:40px; font-family:Arial; max-width:800px; margin:auto;"><h1>' + titleText + '</h1>' + temp.innerHTML + '</div>';
        var blob = new Blob([emailTemplate], { type: 'text/html' });
        var data = [new ClipboardItem({ 'text/html': blob })];
        navigator.clipboard.write(data).then(function() { alert("COPIÉ POUR GMAIL !"); });
    }

    function execStyle(cmd) { document.execCommand(cmd, false, null); }
    function changeTextColor(color) { document.execCommand('foreColor', false, color); }

    window.onload = function() { renderStyles(); };
    </script>
</body>
</html>