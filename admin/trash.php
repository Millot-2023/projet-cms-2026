<?php
/**
 * PROJET-CMS-2026 - GESTION DE LA CORBEILLE
 */

// 1. On remonte d'un dossier pour trouver la config
require_once '../core/config.php';

// 2. Sécurité locale (déjà présent dans ton header, mais bien de le garder ici)
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit("Accès réservé au mode local."); }

$trash_dir = "../content/_trash/";
$archived_projects = [];

// 3. Lecture des projets supprimés
if (is_dir($trash_dir)) {
    $items = scandir($trash_dir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir($trash_dir . $item)) {
            $archived_projects[] = $item;
        }
    }
}

// 4. INCLUSION DU HEADER (On remonte d'un dossier)
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
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($archived_projects as $folder): ?>
                    <div style="border: 1px solid #eee; padding: 25px; border-radius: 12px; background: #fff;">
                        <h3 style="margin: 0 0 10px 0; font-size: 16px;"><?php echo htmlspecialchars($folder); ?></h3>
                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <a href="editor.php?action=restore&slug=<?php echo urlencode($folder); ?>" 
                               style="background: #000; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold;">
                               RESTAURER
                            </a>
                            <a href="editor.php?action=purge&slug=<?php echo urlencode($folder); ?>" 
                               onclick="return confirm('Supprimer définitivement ce dossier ?')"
                               style="background: #ff4d4d; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold;">
                               PURGER
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php 
// 5. INCLUSION DU FOOTER (On remonte d'un dossier)
require_once '../includes/footer.php'; 
?>