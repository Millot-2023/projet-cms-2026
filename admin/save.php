<?php
/**
 * PROJET-CMS-2026 - SAUVEGARDE (VERSION HARMONISÉE)
 * @author: Christophe Millot
 */

require_once '../core/config.php';

// Sécurité : Uniquement accessible en local
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit; }

$data = $_POST;

if ($data && isset($data['slug'])) {
    // Nettoyage du slug pour le nom du dossier
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['slug']));
    $dir = "../content/" . $slug;
    
    if (!file_exists($dir)) { 
        mkdir($dir, 0777, true); 
    }

    $file_path = $dir . "/data.php";
    
    // 1. Récupération des données existantes pour ne pas perdre l'image si inchangée
    $existingData = [];
    if (file_exists($file_path)) {
        // On utilise @ pour éviter les messages d'erreur si le fichier est mal formé
        $loaded = @include $file_path;
        if (is_array($loaded)) { $existingData = $loaded; }
    }

    // 2. Traitement du Design System
    $ds = $data['designSystem'] ?? [];
    if(is_string($ds)) { $ds = json_decode($ds, true); }
    
    $htmlContentRaw = $data['htmlContent'] ?? '';
    $coverValue = $data['coverImage'] ?? ($existingData['cover'] ?? '');

    // 3. Extraction de l'image (Base64 -> Fichier)
    if (strpos($coverValue, 'data:image') === 0) {
        list($type, $coverData) = explode(';', $coverValue);
        list(, $coverData)      = explode(',', $coverData);
        $coverData = base64_decode($coverData);
        $ext = (strpos($type, 'png') !== false) ? 'png' : 'jpg';
        $fileName = "cover." . $ext;
        file_put_contents($dir . "/" . $fileName, $coverData);
        $coverValue = $fileName;
    }

    // 4. CRÉATION DU TABLEAU DE DONNÉES UNIQUE
    // Note : On garde var_export mais on s'assure que les données sont propres
    $finalData = [
        'title'        => $data['title'] ?? ($existingData['title'] ?? 'Sans titre'),
        'cover'        => $coverValue,
        'category'     => $data['category'] ?? ($existingData['category'] ?? 'Design'),
        'date'         => $existingData['date'] ?? date('d.m.Y'),
        'updated'      => date('Y-m-d H:i:s'),
        'summary'      => $data['summary'] ?? ($existingData['summary'] ?? ''),
        'designSystem' => $ds,
        'htmlContent'  => $htmlContentRaw
    ];

    // Génération du contenu du fichier PHP
    $content_file = "<?php\n/** Fichier généré par Studio CMS - " . date('d.m.Y H:i') . " **/\n";
    $content_file .= "return " . var_export($finalData, true) . ";\n";
    $content_file .= "?>";

    // Envoi de la réponse au format JSON (pour ton éditeur en JS)
    header('Content-Type: application/json');
    if (file_put_contents($file_path, $content_file)) {
        echo json_encode(["status" => "success", "message" => "Projet enregistré dans le cockpit !"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur d'écriture dans : " . $file_path]);
    }
}