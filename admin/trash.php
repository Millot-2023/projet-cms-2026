<?php
/**
 * PROJET-CMS-2026 - GESTION DE LA CORBEILLE
 */

require_once '../core/config.php';

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit("Accès réservé au mode local."); }

// Note : Ton chemin pointait vers ../content/_trash/
$trash_dir = "../content/_trash/";
$archived_projects = [];

if (is_dir($trash_dir)) {
    $items = scandir($trash_dir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir($trash_dir . $item)) {
            $archived_projects[] = $item;
        }
    }
}

require_once '../includes/header.php'; 
?>

<main class="trash-page" style="padding-top: 120px; max-width: 1100px; margin: 0 auto;">
    <div style="padding: 0 20px;">
        <h1 style="font-weight: 800; letter-spacing: -1px;">CORBEILLE</h1>
        <p style="color: #666; margin-bottom: 40px;">Projets archivés avant suppression définitive.</p>

        <?php if (empty($archived_projects)): ?>
            <div style="background: #f5f5f5; padding: 60px; text-align: center; border-radius: 8px;">
                <p>La corbeille est vide.</p>
                <a href="<?php echo BASE_URL; ?>index.php" style="color: #000; font-weight: bold;">Retour au Dashboard</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 50px;">
                <?php foreach ($archived_projects as $folder): ?>
                    <div style="border: 1px solid #eee; border-radius: 12px; background: #fff; overflow: hidden; display: flex; flex-direction: column;">
                        
                        <div style="width: 100%; height: 180px; background: #f0f0f0; overflow: hidden;">
                            <?php 
                            $image_path = $trash_dir . $folder . '/cover.jpg';
                            $image_url = BASE_URL . 'content/_trash/' . $folder . '/cover.jpg';
                            
                            if (file_exists($image_path)): ?>
                                <img src="<?php echo $image_url; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Aperçu">
                            <?php else: ?>
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ccc; font-size: 12px;">PAS D'IMAGE</div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 20px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 16px; text-transform: uppercase; font-weight: 700;">
                                <?php echo htmlspecialchars($folder); ?>
                            </h3>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="editor.php?action=restore&slug=<?php echo urlencode($folder); ?>" 
                                   style="background: #000; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: bold;">
                                   RESTAURER
                                </a>
                                <a href="editor.php?action=purge&slug=<?php echo urlencode($folder); ?>" 
                                   onclick="return confirm('Supprimer définitivement ce dossier ?')"
                                   style="border: 1px solid #ff4d4d; color: #ff4d4d; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: bold;">
                                   PURGER
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>