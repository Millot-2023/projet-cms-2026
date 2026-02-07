<?php
/**
 * PROJET-CMS-2026 - SAUVEGARDE (VERSION RIGOUREUSE & OPTIMISÉE)
 * @author: Christophe Millot
 */

require_once '../core/config.php';

$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit; }

$data = $_POST;

if ($data && isset($data['slug'])) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['slug']));
    $dir = "../content/" . $slug;
    
    if (!file_exists($dir)) { 
        mkdir($dir, 0777, true); 
    }

    $file_path = $dir . "/data.php";
    
    // Traitement du Design System
    $ds = $data['designSystem'] ?? [];
    if(is_string($ds)) {
        $ds = json_decode($ds, true);
    }
    
    $htmlContentRaw = $data['htmlContent'] ?? '';

    // --- TRAITEMENT DE L'IMAGE DE COUVERTURE (EXTRACTION DU BASE64) ---
    $coverValue = $data['coverImage'] ?? ($data['cover'] ?? '');
    
    // Si c'est du Base64 (commence par data:image...)
    if (strpos($coverValue, 'data:image') === 0) {
        // Extraction des données binaires
        list($type, $coverData) = explode(';', $coverValue);
        list(, $coverData)      = explode(',', $coverData);
        $coverData = base64_decode($coverData);
        
        // Définition de l'extension
        $ext = 'jpg';
        if (strpos($type, 'png') !== false) { $ext = 'png'; }
        if (strpos($type, 'webp') !== false) { $ext = 'webp'; }
        
        $fileName = "cover." . $ext;
        $fullPath = $dir . "/" . $fileName;
        
        // On sauvegarde le fichier physique
        file_put_contents($fullPath, $coverData);
        
        // On remplace la valeur Base64 par le nom du fichier pour data.php
        $coverValue = $fileName;
    }
    // -----------------------------------------------------------------

    $content_file = "<?php\n";
    $content_file .= "/** Fichier généré par Studio CMS - " . date('d.m.Y H:i') . " **/\n\n";
    $content_file .= "\$title = " . var_export($data['title'] ?? 'Sans titre', true) . ";\n";
    
    // Ici, $coverValue ne contient plus que "cover.jpg" au lieu de 1Mo de texte
    $content_file .= "\$cover = " . var_export($coverValue, true) . ";\n";
    
    $content_file .= "\$category = " . var_export($data['category'] ?? 'Design', true) . ";\n";
    $content_file .= "\$date = " . var_export(date('d.m.Y'), true) . ";\n";
    $content_file .= "\$summary = " . var_export($data['summary'] ?? '', true) . ";\n";
    $content_file .= "\$designSystem = " . var_export($ds, true) . ";\n";
    $content_file .= "\$htmlContent = " . var_export($htmlContentRaw, true) . ";\n";
    $content_file .= "\$content = " . var_export($htmlContentRaw, true) . ";\n"; 
    $content_file .= "?>";

    header('Content-Type: application/json');
    if (file_put_contents($file_path, $content_file)) {
        echo json_encode(["status" => "success", "message" => "Projet publié avec succès !"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur d'écriture."]);
    }
}