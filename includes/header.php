<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet CMS 2026</title>

    <?php 
    // 1. Détection du mode local
    $is_local = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');
    $version = $is_local ? time() : '1.0.0'; 
    
    // 2. Définition du chemin de base absolu
    $base_url = ($is_local) ? '/PROJET-CMS-2026/' : '/'; 

    // 3. Détection d'une action admin pour couper l'animation (évite le clignotement)
    $body_class = isset($_GET['status']) ? 'no-anim' : '';
    ?>
    
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/main.css?v=<?php echo $version; ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=block" rel="stylesheet">

</head>
<body class="<?php echo $body_class; ?>">

<header class="main-header">
    <div class="logo">
        <a href="<?php echo $base_url; ?>index.php">CMS 2026</a>
    </div>
    
    <nav class="nav-container">
        <ul class="nav-links">
            <li><a href="<?php echo $base_url; ?>index.php">Accueil</a></li>
            <li><a href="#">Articles</a></li>
            <!--<li><a href="#">Evolution</a></li>-->
            <li><a href="admin/editor.php">Editeur</a></li>
            
            <?php if ($is_local): ?>
                <li>
                    <a href="<?php echo $base_url; ?>admin/trash.php" class="nav-admin-link">
                        CORBEILLE
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <div class="burger-trigger">
            <span></span>
        </div>
    </nav>
</header>