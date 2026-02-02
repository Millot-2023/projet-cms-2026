<?php
/**
 * PROJET-CMS-2026 - ARCHITECTURE EVOLUTION
 * Index principal
 */
require_once 'core/config.php';

// Affichage des erreurs pour le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Appel du Header
require_once 'includes/header.php'; 
?>

<?php // 2. Le Hero ?>
<?php require_once 'includes/hero.php'; ?>

<div class="master-grid" style="padding-top: 2rem;">
    <main id="main" class="grid-container">

        <?php 
        // DÉTECTION LOCALE POUR LA CARD FANTÔME
        $is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
        if ($is_local): 
        ?>
            <article class="grid-block card-ghost">
                <a href="admin/editor.php?action=new" class="ghost-link">
                    <span class="plus-icon">+</span>
                    <p>NOUVEAU PROJET</p>
                </a>
            </article>
        <?php endif; ?>

        <?php
        // BOUCLE DYNAMIQUE : Scan du dossier content
        $content_path = 'content/';
        if (is_dir($content_path)) {
            // On récupère les dossiers
            $folders = array_diff(scandir($content_path), array('..', '.'));
            
            // Tri décroissant pour avoir les derniers projets en premier
            rsort($folders); 

            foreach ($folders as $folder) {
                // RÈGLE CHRISTOPHE : On ignore les dossiers commençant par "_"
                if (strpos($folder, '_') === 0) continue;

                $data_file = $content_path . $folder . '/data.php';
                
                if (file_exists($data_file)) {
                    include $data_file; // Récupère $title, $summary...
                    ?>
                    <article class="grid-block">
                        <div class="card-content">
                            <h3><?php echo $title; ?></h3>
                            <p><?php echo $summary; ?></p>
                            <a href="article.php?slug=<?php echo $folder; ?>" class="btn-open">Voir le projet</a>
                        </div>
                    </article>
                    <?php
                }
            }
        }
        ?>
    </main>
</div>

<?php 
// 3. Appel du Footer
require_once 'includes/footer.php'; 
?>