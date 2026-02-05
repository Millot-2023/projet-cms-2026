<?php
/**
 * PROJET-CMS-2026 - VUE ARTICLE
 * @author: Christophe Millot
 */
require_once 'core/config.php';

$slug = $_GET['slug'] ?? '';
$data_file = 'content/' . $slug . '/data.php';

if ($slug && file_exists($data_file)) {
    include $data_file;
} else {
    header("Location: index.php");
    exit;
}

require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 

// Sécurité : On s'assure que le contenu ne contient aucune balise 'contenteditable' résiduelle
$finalHtml = str_replace('contenteditable="true"', 'contenteditable="false"', $htmlContent);
?>

<style>
/* 1. RAIL STANDARD 1200PX */
#main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    box-sizing: border-box;
}

/* 2. VERROUILLAGE LECTEUR (CSS) */
/* On empêche toute interaction de type "input" ou "édition" */
.article-reader-mode {
    user-select: text; /* Permet de copier le texte, mais c'est tout */
    cursor: default;
}

.article-reader-mode * {
    outline: none !important;
    border: none !important; /* Supprime les pointillés de l'éditeur */
}

/* 3. DESIGN SYSTEM (FONTS) */
<?php 
if (isset($designSystem)) {
    foreach ($designSystem as $tag => $props) {
        echo ".article-reader-mode $tag { 
            font-size: " . ($props['fontSize'] ?? 'inherit') . "; 
            line-height: " . ($props['lineHeight'] ?? '1.6') . "; 
            text-align: " . ($props['textAlign'] ?? 'left') . "; 
            font-weight: " . ($props['fontWeight'] ?? '400') . "; 
            margin-bottom: 1.5rem;
        }\n";
    }
}
?>

/* 4. DISCRETION ET ITALIQUE */
.meta-discrete {
    font-size: 0.9rem;
    font-style: italic;
    color: #888;
    margin-top: 40px;
    margin-bottom: 20px;
}

/* SUPPRESSION DES ELEMENTS ADMIN */
.article-header, .delete-block, .btn-edit-mode, .admin-only {
    display: none !important;
}
</style>

<main id="main">
    <div class="meta-discrete">
        <?php echo $category; ?> — Publié le <?php echo $date; ?>
    </div>

    <div class="article-reader-mode">
        <?php echo $finalHtml; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>