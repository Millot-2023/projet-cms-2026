<?php
/**
 * PROJET-CMS-2026 - VUE ARTICLE
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
?>

<style>
<?php 
if (isset($designSystem)) {
    foreach ($designSystem as $tag => $props) {
        echo ".article-render $tag { 
            font-size: {$props['fontSize']}; 
            line-height: {$props['lineHeight']}; 
            text-align: {$props['textAlign']}; 
            font-weight: {$props['fontWeight']}; 
        }\n";
    }
}
?>
</style>

<div class="master-grid">
    <main id="main">
        <article class="grid-block">
            <header class="article-header">
                <span class="category"><?php echo $category; ?></span>
                <h1><?php echo $title; ?></h1>
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