<?php
/**
 * CMS-2026 v2.0 - Configuration racine
 * @author: Christophe Millot
 */

define('SITE_NAME', 'CMS-2026 v2.0');
define('BASE_URL', 'http://localhost/cms-2026-v2/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('INC_PATH', __DIR__ . '/../includes/');

// Gestion des dates
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>