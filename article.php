<?php
/**
 * PROJET-CMS-2026 - VUE & ÉDITION ARTICLE
 */
require_once 'core/config.php';

// 1. Récupération des paramètres
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === 'true';
$data_file = 'content/' . $slug . '/data.php';

// 2. Chargement des données
if ($slug && file_exists($data_file)) {
    include $data_file;
} else {
    header("Location: index.php");
    exit;
}

// 3. MOTEUR DE SAUVEGARDE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_article'])) {
    $new_title = addslashes($_POST['title']);
    $new_summary = addslashes($_POST['summary']);
    $new_cat = addslashes($_POST['category']);
    
    // On reconstruit le fichier data.php dynamiquement
    $content = "<?php\n";
    $content .= "\$title = '$new_title';\n";
    $content .= "\$category = '$new_cat';\n";
    $content .= "\$date = '$date';\n"; // On garde la date d'origine
    $content .= "\$summary = '$new_summary';\n";
    $content .= "?>";
    
    if (file_put_contents($data_file, $content)) {
        // Succès : on recharge la page en mode vue
        header("Location: article.php?slug=$slug");
        exit;
    }
}

// 4. Affichage du Header et du Hero
require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 
?>

<div class="master-grid">
    <main id="main">
        <article class="grid-block">
            
            <?php if ($edit_mode): ?>
                <form method="POST" class="edit-form">
                    <header class="article-header">
                        <input type="text" name="category" value="<?php echo isset($category) ? $category : 'Projet'; ?>" class="edit-input-cat">
                        <input type="text" name="title" value="<?php echo $title; ?>" class="edit-input-title">
                        <p class="date"><?php echo $date; ?></p>
                    </header>

                    <div class="article-content">
                        <textarea name="summary" class="edit-textarea"><?php echo $summary; ?></textarea>
                        
                        <div class="admin-controls">
                            <button type="submit" name="save_article" class="btn-save">ENREGISTRER</button>
                            <a href="article.php?slug=<?php echo $slug; ?>" class="btn-cancel">Annuler</a>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <header class="article-header">
                    <span class="category"><?php echo isset($category) ? $category : 'Projet'; ?></span>
                    <h1><?php echo $title; ?></h1>
                    <p class="date"><?php echo $date; ?></p>
                    
                    <?php // Bouton d'accès à l'édition (visible en local uniquement) ?>
                    <?php if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost'): ?>
                        <a href="article.php?slug=<?php echo $slug; ?>&edit=true" class="btn-edit-mode">Modifier ce projet</a>
                    <?php endif; ?>
                </header>

                <div class="article-content">
                    <p class="summary"><?php echo $summary; ?></p>
                    <hr>
                    <div class="work-area">
                        <p>Ceci est l'espace de travail de votre projet. Vous pourrez bientôt y ajouter vos images et descriptions détaillées.</p>
                        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quia vero non ab reiciendis id nam architecto odit fugit hic perferendis consequuntur dolorem.</p>
                    </div>
                </div>
            <?php endif; ?>

        </article>
    </main>
</div>

<?php 
require_once 'includes/footer.php'; 
?>