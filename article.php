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
/* 1. SEUL LE DYNAMIQUE RESTE ICI */
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

<div class="master-grid">
    <main id="main">
        <article class="single-article">
            
            <?php if (!empty($image)): ?>
                <div class="project-main-image">
                    <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
                </div>
            <?php endif; ?>

            <header class="article-header">
                <span class="category"><?php echo $category; ?></span>
                <h1 class="main-article-title"><?php echo $title; ?></h1>
                <p class="date">Publié le <?php echo $date; ?></p>
                <a href="admin/editor.php?slug=<?php echo $slug; ?>" class="btn-edit-mode">Modifier via l'éditeur</a>
            </header>

            <div class="article-content">
                <p class="summary"><em><?php echo $summary; ?></em></p>
                <hr>
                
                <div class="article-render">
                    <?php echo $htmlContent ?? 'Aucun contenu.'; ?>
                </div>
            </div>
        </article>
    </main>
</div>

<?php require_once 'includes/footer.php'; ?>