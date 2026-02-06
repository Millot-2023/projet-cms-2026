<?php
/**
 * PROJET-CMS-2026 - ARCHITECTURE NETTOYÉE
 * Focus : Alignement Catégorie/Date et uniformité visuelle
 * @author: Christophe Millot
 */
require_once 'core/config.php';
require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 
?>

<div class="master-grid">
    <main id="main">
        <h1 class="section-title">Gestion des Projets</h1>

        <div class="grid-container">
            
            <div class="grid-block">
                <div class="card-image">
                    <img src="assets/img/image-template.png" alt="Modèle de projet">
                </div>
                
                <div class="card-content">
                    <header class="article-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <span class="category" style="font-weight: bold; color: #666;">Catégorie</span>
                            <p class="date" style="margin: 0; color: #999;">00 Janvier 2026</p>
                        </div>
                        <h1 class="main-article-title" style="margin-top: 0.5rem;">Titre du Projet</h1>
                    </header>
                    
                    <p>Créer un article...</p>
                    
<div class="card-footer" style="margin-top: auto; padding-top: 1rem;">
    <a href="admin/create_project.php" class="btn-create" style="display: block; text-align: center; background: #000; color: #fff; padding: 0.8rem; border-radius: 8px; text-decoration: none; font-weight: bold;">
        CRÉER
    </a>
</div>
                </div>
            </div>

            <?php
            $content_path = 'content/';
            if (is_dir($content_path)) {
                $folders = array_diff(scandir($content_path), array('..', '.', '_trash'));
                
                foreach ($folders as $folder) {
                    if (strpos($folder, '_') === 0) continue;

                    $project_dir = $content_path . $folder;
                    $data_file = $project_dir . '/data.php';
                    
                    if (file_exists($data_file)) {
                        include $data_file; 
                        ?>
                        <article class="grid-block">
                            <div class="card-image">
                                <?php if(!empty($cover)): ?>
                                    <img src="<?php echo $cover; ?>" alt="<?php echo $title; ?>">
                                <?php else: ?>
                                    <img src="assets/img/image-template.png" alt="Pas d'image">
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-content">
                                <header class="article-header">
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <span class="category" style="font-weight: bold; color: #666;"><?php echo $category; ?></span>
                                        <p class="date" style="margin: 0; color: #999;"><?php echo $date; ?></p>
                                    </div>
                                    <h1 class="main-article-title" style="margin-top: 0.5rem;"><?php echo $title; ?></h1>
                                </header>
                                
                                <p><?php echo substr(strip_tags($summary), 0, 100) . '...'; ?></p>
                                
                                <div class="card-footer" style="margin-top: auto; padding-top: 1rem;">
                                    <a href="article.php?slug=<?php echo $folder; ?>" class="btn-open" style="display: block; text-align: center; border: 2px solid #000; color: #000; padding: 0.8rem; border-radius: 8px; text-decoration: none; font-weight: bold;">
                                        LIRE L'ARTICLE
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                }
            }
            ?>

        </div> </main>
</div>

<?php require_once 'includes/footer.php'; ?>