<?php
/**
 * PROJET-CMS-2026 - SAUVEGARDE
 * @author: Christophe Millot
 */

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit; }

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && isset($data['slug'])) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['slug']));
    $dir = "../content/" . $slug;
    
    if (!file_exists($dir)) { mkdir($dir, 0777, true); }

    $file_path = $dir . "/data.php";
    
    $content = "<?php\n";
    $content .= "\$title = " . var_export($data['title'], true) . ";\n";
    $content .= "\$category = " . var_export($data['category'], true) . ";\n";
    $content .= "\$date = " . var_export(date('d.m.Y'), true) . ";\n";
    $content .= "\$summary = " . var_export($data['summary'], true) . ";\n";
    $content .= "\$designSystem = " . var_export($data['designSystem'], true) . ";\n";
    $content .= "\$htmlContent = " . var_export($data['htmlContent'], true) . ";\n";
    $content .= "?>";

    if (file_put_contents($file_path, $content)) {
        echo json_encode(["status" => "success", "message" => "Projet publié !"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur d'écriture."]);
    }
}