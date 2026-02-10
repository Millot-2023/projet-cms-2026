<?php
/**
 * PROJET-CMS-2026 - CATALOGUE PUBLIC (SIMULATION LECTEUR)
 * @author: Christophe Millot
 */
require_once 'core/config.php';
require_once 'includes/header.php'; 
require_once 'includes/hero.php'; 
?>

<div class="master-grid">
    <main id="main">
        <h1 class="section-title">Simulation : Vue Lecteur</h1>

        <div class="grid-container">
            <?php
            $content_path = 'content/';
            if (is_dir($content_path)) {
                $folders = array_diff(scandir($content_path), array('..', '.', '_trash'));
                
                foreach ($folders as $folder) {
                    if (strpos($folder, '_') === 0 || !is_dir($content_path . $folder)) continue;

                    $project_dir = $content_path . $folder;
                    $data_file = $project_dir . '/data.php';
                    
                    if (file_exists($data_file)) {
                        $data_loaded = include $data_file;

                        $title = "Sans titre";
                        $category = "Design";
                        
                        // 1. ON FORCE LA DATE DU DOSSIER PAR DÉFAUT
                        $date = date("d.m.Y", filemtime($project_dir));
                        
                        $cover = "";
                        $summary = "";

                        if (is_array($data_loaded)) {
                            $title = $data_loaded['title'] ?? $title;
                            $category = $data_loaded['category'] ?? $category;
                            
                            // 2. ON NE REMPLACE QUE SI LA DATE PHP SEMBLE VALIDE (contient au moins un chiffre)
                            if (!empty($data_loaded['date']) && preg_match('/[0-9]/', $data_loaded['date'])) {
                                $date = $data_loaded['date'];
                            }
                            
                            $cover = $data_loaded['cover'] ?? "";
                            $summary = $data_loaded['summary'] ?? "";
                        }

                        $image_src = "assets/img/image-template.png";
                        if (!empty($cover)) {
                            $image_src = (strpos($cover, 'data:image') === 0) ? $cover : $content_path . $folder . '/' . $cover;
                        }
                        ?>
                        
                        <article class="grid-block">
                            <div class="card-image">
                                <img src="<?php echo $image_src; ?>" alt="">
                            </div>
                            
                            <div class="card-content">
                                <header class="article-header">
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; margin-bottom: 0.5rem; text-transform: uppercase;">
                                        <span class="category" style="font-weight: bold; color: #888;"><?php echo htmlspecialchars($category); ?></span>
                                        <p class="date" style="margin: 0; color: #666;"><?php echo htmlspecialchars($date); ?></p>
                                    </div>
                                    <h1 class="main-article-title"><?php echo htmlspecialchars($title); ?></h1>
                                </header>
                                
                                <p><?php echo mb_strimwidth(strip_tags($summary), 0, 140, "..."); ?></p>
                                
                                <div class="card-footer" style="margin-top: auto;">
                                    <a href="article.php?slug=<?php echo urlencode($folder); ?>" class="btn-read" style="display: block; text-align: center; background: #000; color: #fff; padding: 0.8rem; border-radius: 8px; text-decoration: none; font-weight: bold;">
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
        </div> 
    </main>
</div>

<?php require_once 'includes/footer.php'; ?>