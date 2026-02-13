<?php
/**
 * PROJET-CMS-2026 - ÉDITEUR DESIGN SYSTEM (VERSION v3.0.6-FIDÈLE)
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
                <div class="color-wrapper" style="position: relative; width: 100%; height: 40px; border: 1px solid var(--sidebar-border); border-radius: 4px; overflow: hidden; background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);">
                    <input type="color" oninput="changeTextColor(this.value)" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                </div>
            </div>

            <span class="section-label">RÉGLAGES : <span id="target-label" style="color:#fff">H1</span></span>
            <div class="gauge-row">
                <div class="gauge-info"><span>TAILLE POLICE</span><span id="val-size">64</span>px</div>
                <input type="range" id="slider-size" min="8" max="120" value="64" oninput="updateStyle('fontSize', this.value+'px', 'val-size')">
            </div>

            <span class="section-label">DISPOSITION (FLOAT)</span>
            <div class="row-float">
                <button class="tool-btn" onclick="addFloatBlock('left')" title="Aligner à gauche">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="11" width="9" height="2" fill="currentColor"/><rect x="11" y="4" width="9" height="2" fill="currentColor"/><rect y="8" width="20" height="2" fill="currentColor"/><rect y="12" width="20" height="2" fill="currentColor"/><rect width="9" height="6" fill="currentColor" stroke="black" stroke-width="1"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('full')" title="Pleine largeur">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="8" width="20" height="2" fill="currentColor"/><rect y="12" width="20" height="2" fill="currentColor"/><rect width="20" height="6" fill="currentColor"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('right')" title="Aligner à droite">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="9" height="2" fill="currentColor"/><rect width="9" y="4" height="2" fill="currentColor"/><rect y="8" width="20" height="2" fill="currentColor"/><rect y="12" width="20" height="2" fill="currentColor"/><rect x="11" width="9" height="6" fill="currentColor" stroke="black" stroke-width="1"/></svg>
                </button>
            </div>
            <div class="row-float" style="margin-top:5px;">
                <button class="tool-btn" onclick="addFloatBlock('bottom-left')" title="Aligner en bas à gauche">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="0" width="20" height="2" fill="currentColor"/><rect y="4" width="20" height="2" fill="currentColor"/><rect x="11" y="8" width="9" height="2" fill="currentColor"/><rect x="11" y="12" width="9" height="2" fill="currentColor"/><rect width="9" height="6" y="8" fill="currentColor" stroke="black" stroke-width="1"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('bottom-full')" title="Pleine largeur en bas">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="0" width="20" height="2" fill="currentColor"/><rect y="4" width="20" height="2" fill="currentColor"/><rect y="8" width="20" height="6" fill="currentColor"/></svg>
                </button>
                <button class="tool-btn" onclick="addFloatBlock('bottom-right')" title="Aligner en bas à droite">
                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="9" height="2" fill="currentColor"/><rect width="9" y="4" height="2" fill="currentColor"/><rect y="8" width="20" height="2" fill="currentColor"/><rect y="12" width="20" height="2" fill="currentColor"/><rect x="11" y="8" width="9" height="6" fill="currentColor" stroke="black" stroke-width="1"/></svg>
                </button>
            </div>

            <span class="section-label">JUSTIFICATION TEXTE</span>
            <div class="row-justify" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 15px;">
                <button class="tool-btn" onclick="setTextJustify('left')">L</button>
                <button class="tool-btn" onclick="setTextJustify('center')">C</button>
                <button class="tool-btn" onclick="setTextJustify('right')">R</button>
                <button class="tool-btn" onclick="setTextJustify('full')">J</button>
            </div>

            <span class="section-label">COLONNES</span>
            <div class="row-cols" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 8px;">
                <button class="tool-btn" onclick="addGridBlock(2)" style="font-size: 10px; font-weight: bold;">2 COLONNES</button>
                <button class="tool-btn" onclick="addGridBlock(3)" style="font-size: 10px; font-weight: bold;">3 COLONNES</button>
            </div>

            <div onclick="toggleLettrine()" style="width: 100%; margin-bottom: 12px; display: flex; align-items: center; justify-content: flex-start; gap: 10px; cursor: pointer; padding: 5px 0;">
                <span id="v-icon" style="display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border: 1px solid #ccc; border-radius: 3px; font-weight: bold; font-size: 11px; color: transparent;">V</span> 
                <span style="color: #bbb; font-size: 9px; text-transform: uppercase;">Lettrine</span>
            </div>

            <div class="gauge-row">
                <div class="gauge-info"><span>ESPACEMENT</span><span id="val-gutter">20</span>px</div>
                <input type="range" id="slider-gutter" min="0" max="100" value="20" oninput="updateGutter(this.value)">
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
            <div id="editor-core"><?php echo $htmlContent; ?></div> 
        </article>
    </main>

    <script>
    var coverData = "<?php echo $cover; ?>"; 
    var currentTag = 'h1';
    var currentImageElement = null;
    var currentTargetElement = null;
    var designSystem = <?php echo json_encode($designSystemArray); ?>;
    var LOREM_TEXT = "Le design system n'est pas seulement une collection de composants, c'est l'ossature de votre projet web. En utilisant des blocs structurés, vous assurez une cohérence visuelle sur tous les écrans, du mobile au desktop. Ce texte permet de tester la lisibilité, l'espacement des lignes et l'impact des lettrines sur vos paragraphes. Un bon éditeur doit permettre de manipuler ces éléments avec fluidité pour obtenir un rendu professionnel et équilibré à chaque publication.";

    function renderStyles() {
        var dynStyle = document.getElementById('dynamic-styles');
        if(!dynStyle) return;
        var css = ".paper { padding-top: 40px; }\n"; 

        // Règle Lettrine Rétablie
        css += ".has-lettrine::first-letter { float: left; font-size: 3.5rem; line-height: 1; margin-right: 12px; font-weight: 900; display: block; padding: 4px; font-family: serif; }\n";

        for (var tag in designSystem) {
            css += ".paper " + tag + " { font-size: " + designSystem[tag].fontSize + "; line-height: 1.1; margin-bottom: 1.5em; }\n";
        }
        dynStyle.innerHTML = css;
    }

    function setTarget(tag, el) {
        currentTag = tag;
        currentImageElement = (tag === 'grid' || tag === 'img') ? el : null;
        // Correction ici : Accepte les col-item directement pour la lettrine
        currentTargetElement = (el && (el.getAttribute('contenteditable') === 'true' || el.classList.contains('col-item'))) ? el : (el.querySelector ? el.querySelector('p') : null);
        
        var label = document.getElementById('target-label');
        if(label) label.innerText = tag.toUpperCase();
        updateLettrineIcon(currentTargetElement);
    }

    function toggleLettrine() {
        if (currentTargetElement) {
            currentTargetElement.classList.toggle('has-lettrine');
            updateLettrineIcon(currentTargetElement);
        }
    }

    function updateLettrineIcon(target) {
        let icon = document.getElementById('v-icon');
        if (!icon) return;
        let active = target && target.classList.contains('has-lettrine');
        icon.style.backgroundColor = active ? "#28a745" : "transparent";
        icon.style.borderColor = active ? "#28a745" : "#ccc";
        icon.style.color = active ? "#fff" : "transparent";
    }

    function addBlock(tag, txt) {
        txt = txt || LOREM_TEXT;
        var container = document.createElement('div');
        container.className = 'block-container';
        container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div><' + tag + ' contenteditable="true" onfocus="setTarget(\'' + tag + '\', this)">' + txt + '</' + tag + '>';
        document.getElementById('editor-core').appendChild(container);
    }

    function addGridBlock(num) {
        var container = document.createElement('div');
        container.className = 'block-container';
        var colsHtml = '';
        for(var i=0; i < num; i++) {
            colsHtml += '<div class="col-item" contenteditable="true" onfocus="setTarget(\'grid\', this)">' + LOREM_TEXT + '</div>';
        }
        container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div><div class="grid-wrapper">' + colsHtml + '</div>';
        document.getElementById('editor-core').appendChild(container);
    }




/*

function addFloatBlock(type) {
    var container = document.createElement('div');
    container.className = 'block-container';
    var style = (type === 'left') ? "float:left; margin:0 20px 10px 0; width:40%;" : (type === 'right') ? "float:right; margin:0 0 10px 20px; width:40%;" : "width:100%; margin-bottom:20px; clear:both;";
    
    container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div><div class="image-placeholder" onclick="setTarget(\'img\', this); document.getElementById(\'inp-cover\').click();" style="' + style + ' background:#eee; aspect-ratio:16/9; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; position:relative;">IMAGE</div><p contenteditable="true" onfocus="setTarget(\'p\', this)">' + LOREM_TEXT + '</p>';
    
    document.getElementById('editor-core').appendChild(container);
}*/



















function updateImage(imgElement) {
    const newSrc = prompt("Entrez l'URL de la nouvelle image :", imgElement.src);
    
    if (newSrc) {
        console.log("Nouvelle URL reçue :", newSrc); // TEST 1
        
        imgElement.src = newSrc;
        
        if (typeof updateSidebarCardImage === "function") {
            updateSidebarCardImage(imgElement);
            console.log("Sidebar mise à jour"); // TEST 2
        }

        console.log("Tentative de sauvegarde..."); // TEST 3
        saveContent(); 
    }
}




function updateSidebarCardImage(imgElement) {
    // 1. On trouve le bloc parent dans l'éditeur
    const container = imgElement.closest('.block-container');
    
    // 2. On calcule sa position (index) parmi tous les blocs
    const allBlocks = Array.from(document.querySelectorAll('#paper .block-container'));
    const index = allBlocks.indexOf(container);
    
    // 3. On cible l'image dans la sidebar avec ton nouveau HTML (.card-image img)
    const sidebarImages = document.querySelectorAll('.sidebar .card-image img');
    const targetImg = sidebarImages[index];

    if (targetImg) {
        // 4. On synchronise la source
        targetImg.src = imgElement.src;
    }
}







function saveContent() {
    const blocks = [];
    
    document.querySelectorAll('#paper .block-container').forEach(container => {
        // On cherche l'image soit directement, soit dans le placeholder
        const img = container.querySelector('img');
        const textElement = container.querySelector('p');

        blocks.push({
            // On sauve le src de l'image trouvée, sinon le template par défaut
            image: img ? img.getAttribute('src') : "assets/img/image-template.png",
            content: textElement ? textElement.innerHTML : ""
        });
    });

    console.log("Données envoyées :", blocks);

    fetch('save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(blocks)
    })
    .then(response => response.json())
    .then(data => console.log("Sauvegarde réussie :", data))
    .catch(err => console.error("Erreur :", err));
}























function addFloatBlock(type) {
    var container = document.createElement('div');
    container.className = 'block-container';
    var style = (type === 'left') ? "float:left; margin:0 20px 10px 0; width:40%;" : (type === 'right') ? "float:right; margin:0 0 10px 20px; width:40%;" : "width:100%; margin-bottom:20px; clear:both;";
    
    container.innerHTML = '<div class="delete-block" onclick="this.parentElement.remove()">✕</div>' +
        '<div class="image-placeholder" ' +
        'onclick="setTarget(\'img\', this)" ' +
        'ondblclick="document.getElementById(\'inp-cover\').click();" ' + 
        'style="' + style + ' background:#eee; aspect-ratio:16/9; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; position:relative;">' +
        'IMAGE</div>' +
        '<p contenteditable="true" onfocus="setTarget(\'p\', this)">' + LOREM_TEXT + '</p>';
    
    document.getElementById('editor-core').appendChild(container);
}





































    function resizePaper(width) {
        var paper = document.getElementById('paper');
        paper.style.width = width;
        paper.style.maxWidth = (width === '100%') ? "850px" : width;
    }

    function updateStyle(prop, val, displayId) {
        if(designSystem[currentTag]) {
            designSystem[currentTag][prop] = val;
            document.getElementById(displayId).innerText = val.replace('px','');
            renderStyles();
        }
    }

    function updateImageWidth(val) { if(currentImageElement) { currentImageElement.style.width = val + '%'; document.getElementById('val-img-width').innerText = val; } }
    function updateGutter(val) { if(currentTag === 'grid' && currentImageElement) { currentImageElement.style.gap = val + 'px'; document.getElementById('val-gutter').innerText = val; } }
    function setTextJustify(type) { if (currentTargetElement) { currentTargetElement.style.textAlign = (type === 'full') ? 'justify' : type; } }
    function execStyle(cmd) { document.execCommand(cmd, false, null); }
    function changeTextColor(color) { document.execCommand('foreColor', false, color); }
    function toggleSidebar() { document.body.classList.toggle('sidebar-hidden');}
    function toggleTheme() { document.body.classList.toggle('light-mode'); }




/*
    function handleCoverChange(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                coverData = e.target.result;
                document.getElementById('preview-container').innerHTML = '<img src="' + coverData + '">';
            };
            reader.readAsDataURL(file);
        }
    }*/

function handleCoverChange(input) {
    var file = input.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var imageData = e.target.result;
            
            if (currentTag === 'img' && currentImageElement) {
                // On insère l'image dans le placeholder
                currentImageElement.innerHTML = '<img src="' + imageData + '" style="width:100%; height:100%; object-fit:cover;">';
                
                // --- SYNCHRO SIDEBAR ---
                // On passe l'élément <img> qu'on vient de créer à la synchro
                updateSidebarCardImage(currentImageElement.querySelector('img'));
                
                // --- SAUVEGARDE ---
                saveContent(); 
                
                currentTag = null; 
            } 
            else {
                coverData = imageData;
                var preview = document.getElementById('preview-container');
                if(preview) {
                    preview.innerHTML = '<img src="' + imageData + '">';
                }
            }
        };
        reader.readAsDataURL(file);
    }
}




    function publishProject() {
        var formData = new FormData();
        formData.append('slug', document.getElementById('inp-slug').value);
        formData.append('htmlContent', document.getElementById('editor-core').innerHTML);
        formData.append('designSystem', JSON.stringify(designSystem));
        fetch('save.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(d => alert(d.message));
    }








    window.onload = renderStyles;






    </script>
</body>
</html>