<?php
/**
 * PROJET-CMS-2026 - ARCHITECTURE EVOLUTION
 * Index principal avec Cards Graphiques
 */
require_once 'core/config.php';

// Affichage des erreurs pour le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 
?>

<div class="master-grid">

    <main id="main">
        
        <h1 class="section-title">Dernières Publications</h1>

        <div class="grid-container">

            <?php 
            // 1. DÉTECTION LOCALE POUR LA CARD FANTÔME ET ACTIONS ADMIN
            $is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
            if ($is_local): 
            ?>
                <article class="grid-block card-ghost">
                    <div class="card-image">
                        <img src="assets/img/template-ghost.jpg" alt="Nouveau Projet">
                    </div>
                    <div class="card-content">
                        <div class="card-meta">
                            <span class="category">ADMIN</span>
                            <span class="date"><?php echo date('d.m.Y'); ?></span>
                        </div>
                        <h3>NOUVEAU PROJET</h3>
                        <p class="summary">Prêt à documenter une nouvelle création ? Cliquez pour générer le dossier.</p>
                        <div class="card-action">
                            <a href="admin/editor.php?action=new" class="btn-open">CRÉER</a>
                        </div>
                    </div>
                </article>
            <?php endif; ?>

            <?php
            // 2. BOUCLE DYNAMIQUE : Scan du dossier content
            $content_path = 'content/';
            if (is_dir($content_path)) {
                // On exclut le dossier _trash de la liste pour ne pas afficher les archives
                $folders = array_diff(scandir($content_path), array('..', '.', '_trash'));
                rsort($folders); 

                foreach ($folders as $folder) {
                    // Sécurité supplémentaire : ignore tout dossier commençant par _
                    if (strpos($folder, '_') === 0) continue;

                    $project_dir = $content_path . $folder;
                    $data_file = $project_dir . '/data.php';
                    $thumb_file = $project_dir . '/thumb.jpg';
                    
                    if (file_exists($data_file)) {
                        include $data_file; 
                        
                        $image_src = file_exists($thumb_file) ? $thumb_file : 'assets/img/placeholder.jpg';
                        ?>
                        <article class="grid-block">
                            
                            <?php if ($is_local): ?>
                                <a href="admin/editor.php?action=delete&slug=<?php echo $folder; ?>" 
                                   class="btn-delete-card" 
                                   onclick="return confirm('Voulez-vous vraiment archiver ce projet ?');"
                                   title="Archiver le projet">
                                   &times;
                                </a>
                            <?php endif; ?>

                            <div class="card-image">
                                <img src="<?php echo $image_src; ?>" alt="<?php echo $title; ?>">
                            </div>
                            
                            <div class="card-content">
                                <div class="card-meta">
                                    <span class="category"><?php echo $category; ?></span>
                                    <span class="date"><?php echo $date; ?></span>
                                </div>
                                
                                <h3><?php echo $title; ?></h3>
                                
                                <p class="summary"><?php echo $summary; ?></p>
                                
                                <div class="card-action">
                                    <a href="article.php?slug=<?php echo $folder; ?>" class="btn-open">Lire l'article</a>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                }
            }
            ?>
        </div> 
    </main>

</div>

<?php require_once 'includes/footer.php'; ?>