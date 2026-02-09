<?php
/**
 * PROJET-CMS-2026 - GESTION DE LA CORBEILLE (FIX WARNING & NAV)
 * @author: Christophe Millot
 */
require_once '../core/config.php';

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit; }

include '../includes/header.php'; 
?>

<div class="master-grid" style="padding-top: 100px;">
    <main id="main">
        <header class="section-header" style="margin-bottom: 2rem;">
            <h1 class="section-title">Corbeille (Archives)</h1>
            <p><a href="<?php echo BASE_URL; ?>index.php" style="text-decoration: none; color: #666;">← Retour au Dashboard</a></p>
        </header>

        <div class="grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php
            $trash_path = '../content/_trash/';
            if (is_dir($trash_path)) {
                $folders = array_diff(scandir($trash_path), array('..', '.'));
                foreach ($folders as $folder) {
                    $project_dir = $trash_path . $folder;
                    $data_file = $project_dir . '/data.php';

                    if (file_exists($data_file)) {
                        // FIX : Inclusion sécurisée
                        $project_data = include $data_file;

                        $display_title = "Sans titre";
                        $display_summary = "Aucun résumé.";
                        $display_cat = 'PROJET';

                        if (is_array($project_data)) {
                            $display_title = $project_data['title'] ?? $display_title;
                            $display_summary = $project_data['summary'] ?? $display_summary;
                            $display_cat = $project_data['category'] ?? 'PROJET';
                        } else {
                            // Support variables globales si le include ne retourne pas de tableau
                            if (isset($title)) { $display_title = $title; }
                            if (isset($summary)) { $display_summary = $summary; }
                            if (isset($category)) { $display_cat = $category; }
                        }

                        $cover_path = $project_dir . '/cover.jpg';
                        $cover_url = file_exists($cover_path) ? $cover_path : BASE_URL . 'assets/img/image-template.png';

                        $parts = explode('_', $folder, 2);
                        $date_f = (isset($parts[0]) && strlen($parts[0]) >= 8) ? substr($parts[0], 6, 2) . '/' . substr($parts[0], 4, 2) . '/' . substr($parts[0], 0, 4) : "??/??/????";
                        ?>
                        
                        <article class="grid-block" style="filter: grayscale(0.5); border: 1px solid #ddd; position: relative; background:#fff;">
                            <div style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: #fff; padding: 4px 8px; font-size: 0.6rem; font-weight: bold; border-radius: 4px; z-index: 10;">
                                ARCHIVE : <?php echo $date_f; ?>
                            </div>

                            <div class="card-image" style="height: 200px; overflow: hidden; background: #eee;">
                                <img src="<?php echo $cover_url; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>

                            <div class="card-content" style="padding: 20px;">
                                <div class="card-meta">
                                    <span class="category" style="color:#ff4444;"><?php echo htmlspecialchars($display_cat); ?></span>
                                </div>
                                <h3 class="card-title" style="margin-top:10px; font-weight:bold;"><?php echo htmlspecialchars($display_title); ?></h3>
                                <p class="card-summary" style="font-size: 0.8rem; color: #666; margin-bottom:20px;"><?php echo htmlspecialchars($display_summary); ?></p>
                                
                                <div class="card-action" style="display: flex; gap: 10px;">
                                    <a href="editor.php?action=restore&slug=<?php echo urlencode($folder); ?>" 
                                       class="btn-open" style="background: #222; color: #fff; flex: 1; text-align: center; padding: 12px; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight:bold;">
                                         RESTAURER
                                    </a>
                                    <a href="editor.php?action=purge&slug=<?php echo urlencode($folder); ?>" 
                                       style="color: #ff4444; font-size: 0.7rem; align-self: center; text-decoration: underline;"
                                       onclick="return confirm('Action irréversible !');">
                                         DÉTRUIRE
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

<?php include '../includes/footer.php'; ?>