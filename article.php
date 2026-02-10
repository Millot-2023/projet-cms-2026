<?php
/**
 * PROJET-CMS-2026 - VUE ARTICLE (VERSION CORRIGÉE COLONNES)
 * @author: Christophe Millot
 */
require_once 'core/config.php';

$slug = $_GET['slug'] ?? '';
$data_file = 'content/' . $slug . '/data.php';

if ($slug && file_exists($data_file)) {
    // 1. CHARGEMENT
    $data_loaded = include $data_file;

    // 2. EXTRACTION ET VALEURS PAR DÉFAUT
    if (is_array($data_loaded)) {
        $title        = $data_loaded['title'] ?? "Sans titre";
        $category     = $data_loaded['category'] ?? "Design";
        $date         = $data_loaded['date'] ?? date('d/m/Y');
        $summary      = $data_loaded['summary'] ?? "";
        $designSystem = $data_loaded['designSystem'] ?? [];
        $htmlContent  = $data_loaded['htmlContent'] ?? 'Aucun contenu.';
    } else {
        // Au cas où le fichier existe mais est vide ou mal formé
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
} else {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 

// Sécurité : Nettoyage des attributs d'édition pour le visiteur
$finalHtml = str_replace('contenteditable="true"', '', $htmlContent);
?>

<style>
/* 1. RENDU DYNAMIQUE DU DESIGN SYSTEM */
<?php 
if (isset($designSystem) && is_array($designSystem)) {
    foreach ($designSystem as $tag => $props) {
        $fontSize = $props['fontSize'] ?? 'inherit';
        echo ".article-render $tag { 
            font-size: $fontSize; 
            line-height: 1.6; 
            margin-bottom: 1.5rem;
        }\n";
    }
}
?>

/* 2. STRUCTURE ET FLUX */
.master-grid {
    clear: both;
    position: relative;
    z-index: 10;
    background: #fff;
}

.single-article {
    padding: 40px 20px;
    max-width: 900px;
    margin: 0 auto;
}

.article-render img {
    max-width: 100%;
    height: auto;
    display: block;
}

/* --- FIX COLONNES ÉDITEUR --- */
.editor-grid { 
    display: flex !important; 
    flex-direction: row !important; 
    flex-wrap: wrap !important; 
    gap: 20px !important; 
    width: 100% !important;
    margin: 2rem 0 !important;
}

.editor-grid > div { 
    flex: 1 !important; 
    min-width: 250px !important; 
}

/* --- COMPATIBILITÉ ANCIENS BLOCS --- */
.grid-block:not(.article-header-view) {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 20px !important;
}

/* CLEARFIX POUR LES FLOATS DANS LE RENDU */
.article-render::after, .float-block::after {
    content: ""; display: table; clear: both;
}

/* SUPPRESSION DES RELIQUATS ADMIN */
.delete-block, .hidden-file-input {
    display: none !important;
}
</style>

<div class="master-grid">
    <main id="main">
        <article class="single-article">
            
            <header class="article-header-view">
                <span class="category" style="text-transform: uppercase; letter-spacing: 2px; font-size: 12px; color: #666;">
                    <?php echo htmlspecialchars($category); ?>
                </span>
                <h1 class="main-article-title"><?php echo htmlspecialchars($title); ?></h1>
                <p class="date" style="font-size: 13px; color: #999;">Publié le <?php echo htmlspecialchars($date); ?></p>
            </header>

            <div class="article-content">
                <?php if(!empty($summary)): ?>
                    <p class="summary" style="font-size: 1.2rem; line-height: 1.6; color: #444; margin: 30px 0; border-left: 3px solid #000; padding-left: 20px;">
                        <em><?php echo nl2br(htmlspecialchars($summary)); ?></em>
                    </p>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">
                <?php endif; ?>
                
                <div class="article-render">
                    <?php echo $finalHtml; ?>
                </div>
            </div>
            
            <footer style="margin-top: 60px; padding-top: 20px; border-top: 1px solid #eee;">
                 <p><a href="<?php echo BASE_URL; ?>index.php" style="text-decoration: none; color: #000; font-weight: bold;">← Retour au Dashboard</a></p>
            </footer>
        </article>
    </main>
</div>

<?php require_once 'includes/footer.php'; ?>