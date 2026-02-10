<?php
/**
 * PROJET-CMS-2026 - MISE À LA CORBEILLE (ARCHIVAGE)
 * @author: Christophe Millot
 */

require_once '../core/config.php';

// Sécurité locale
$is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
if (!$is_local) { exit; }

if (isset($_GET['project'])) {
    // Nettoyage rigoureux du nom
    $project = preg_replace('/[^A-Za-z0-9-]+/', '-', $_GET['project']);
    $source = "../content/" . $project;
    
    // Préparation de la destination avec le format Ymd-His_nom
    $trash_root = "../content/_trash/";
    $timestamp = date("Ymd-His");
    $destination = $trash_root . $timestamp . "_" . $project;

    if (is_dir($source)) {
        // Création du dossier _trash si inexistant
        if (!is_dir($trash_root)) {
            mkdir($trash_root, 0777, true);
        }

        // DEPLACEMENT (RENAME)
        if (rename($source, $destination)) {
            // Utilisation de BASE_URL pour une redirection robuste
            header("Location: " . BASE_URL . "index.php?status=trashed");
            exit;
        }
    }
}

// Retour au Cockpit en cas d'erreur ou d'accès direct
header("Location: " . BASE_URL . "index.php?status=error");
exit;