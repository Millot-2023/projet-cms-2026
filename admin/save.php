<?php
/**
 * PROJET-CMS-2026 - MOTEUR DE SAUVEGARDE V4
 */

header('Content-Type: application/json');
require_once '../core/config.php';

// Sécurité locale
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) {
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

// 1. Récupération des données du formulaire
$data = $_POST; // On utilise directement $_POST pour remplir notre tableau $data
$slug = $data['slug'] ?? '';

if (empty($slug)) {
    echo json_encode(['status' => 'error', 'message' => 'Le slug est manquant.']);
    exit;
}

// Nettoyage du slug et création du dossier
$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($slug));
$dir = "../content/" . $slug;

if (!file_exists($dir)) { 
    mkdir($dir, 0777, true); 
}

$file_path = $dir . "/data.php";

// 2. Récupération des données existantes
$existingData = [];
if (file_exists($file_path)) {
    $loaded = @include $file_path;
    if (is_array($loaded)) { $existingData = $loaded; }
}

// 3. Traitement du Design System (JSON -> Array)
$ds = $data['designSystem'] ?? [];
if(is_string($ds)) { $ds = json_decode($ds, true); }

// 4. Gestion de la cover (Base64 ou existante)
$coverValue = $data['coverImage'] ?? ($existingData['cover'] ?? '');

if (strpos($coverValue, 'data:image') === 0) {
    list($type, $imgData) = explode(';', $coverValue);
    list(, $imgData)      = explode(',', $imgData);
    $imgData = base64_decode($imgData);
    $ext = (strpos($type, 'png') !== false) ? 'png' : 'jpg';
    $fileName = "cover." . $ext;
    file_put_contents($dir . "/" . $fileName, $imgData);
    $coverValue = $fileName;
}

// 5. Préparation du tableau final
$finalData = [
    'title'        => $data['title'] ?? ($existingData['title'] ?? 'Sans titre'),
    'cover'        => $coverValue,
    'category'     => $data['category'] ?? ($existingData['category'] ?? 'Design'),
    'date'         => $existingData['date'] ?? date('d.m.Y'),
    'updated'      => date('Y-m-d H:i:s'),
    'summary'      => $data['summary'] ?? ($existingData['summary'] ?? ''),
    'designSystem' => $ds,
    'htmlContent'  => $data['htmlContent'] ?? ''
];

// 6. Écriture du fichier
$content_file = "<?php\n/** Fichier généré par Studio CMS - " . date('d.m.Y H:i') . " **/\n";
$content_file .= "return " . var_export($finalData, true) . ";\n";
$content_file .= "?>";

if (file_put_contents($file_path, $content_file)) {
    echo json_encode(["status" => "success", "message" => "Projet enregistré avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur d'écriture dans : " . $file_path]);
}