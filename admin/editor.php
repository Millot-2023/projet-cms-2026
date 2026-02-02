<?php
/**
 * PROJET-CMS-2026 - ÉDITEUR MÉCANIQUE
 */

// Détection directe
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');

if (!$is_local) {
    die("Acces reserve.");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'new') {
    $new_slug = 'projet-' . date('Ymd-His');
    $target_path = '../content/' . $new_slug;

    if (!is_dir($target_path)) {
        mkdir($target_path, 0777, true);
        
        $d = date('Y-m-d');
        // Syntaxe sécurisée pour VS Code (pas de dollar dans les strings)
        $php_start = "<?php\n";
        $var_title = chr(36) . "title = 'Nouveau Projet';\n";
        $var_cat   = chr(36) . "category = 'Design';\n";
        $var_date  = chr(36) . "date = '" . $d . "';\n";
        $var_sum   = chr(36) . "summary = 'Résumé à éditer...';\n";
        $php_end   = "?>";
        
        $final_content = $php_start . $var_title . $var_cat . $var_date . $var_sum . $php_end;
        
        file_put_contents($target_path . '/data.php', $final_content);
        
        header("Location: ../article.php?slug=" . $new_slug);
        exit;
    }
}
?>