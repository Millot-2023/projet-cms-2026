<?php
/**
 * PROJET-CMS-2026 - ARCHITECTURE EVOLUTION
 * Index principal
 */

// Affichage des erreurs pour le développement sous XAMPP
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Chemins de base
define('ASSETS_URL', 'assets/');

// 1. Appel du Header
require_once 'includes/header.php'; 
?>

<?php // 2. Le Hero est placé ici, AVANT la grille, pour être Full Viewport ?>
<?php require_once 'includes/hero.php'; ?>

<div class="master-grid">

    <main id="main">

        <article class="grid-block">
            <h1>Bienvenue dans Evolution</h1>
            <h2>Le projet</h2>
            <h3>Création d'un site web avec son CMS intégré en mode admin</h3>

            <ul>
                <li><b>&#9989;A1 - Ajout, modification et suppression d'articles :</b> <b>CRUD</b> (Create, Read, Update, Delete) classique. La question sera de savoir si on stocke tes articles dans des fichiers JSON ou dans une base de données SQL ?</li>
                <li><b>&#9989;A2 - Gestion des médias :</b> Téléchargement et redimensionnement automatique des images pour optimiser le poids du site final.</li>
                
                <hr>
                
                <li><b>&#128992;B1 - <u>Garantir la sécurité du mode admin / prévoir un site sans admin</u></b></li>
                <p><b>Explications :</b> L'idée est de séparer physiquement l'interface de gestion du site public. En production, on peut supprimer le dossier admin ou le désactiver totalement.</p>
                
                <li><b>&#128992;B3 - <u>Authentification : implémenter un système de jetons (tokens) ou une double authentification par mail.</u></b></li>
                <p><b>Explications :</b> Sécurité maximale à distance via code temporaire par mail. En local, le système détecte l'environnement de développement et maintient une session persistante.</p>
                
                <li><b>&#128992;B4 - <u>Permissions : distinction entre un mode "Édition" (on modifie le contenu) et un mode "Structure" (on touche au code/design).</u></b></li>
                <p><b>Explications :</b> Il s'agit de brider l'interface selon le besoin. Cela évite les erreurs de manipulation fatales au site.</p>
                
                <hr>

                <li><b>&#9989;C1 - <u>Ergonomie de l'interface : le panneau escamotable</u></b></li>
                <p><b>Explications :</b> Mise en place d'un système de "tiroir" (Toggle) via l'icône engrenage. Le panneau de contrôle se replie pour libérer totalement le champ de vision.</p>
                
                <li><b>&#9989;C2 - <u>Modularité par Blocs : Manifeste, Image et Grid Creator</u></b></li>
                <p><b>Explications :</b> Construction de la page par empilement de composants spécifiques. Chaque bloc possède ses propres réglages.</p>
                
                <li><b>&#9989;C3 - <u>Persistance de l'état de l'interface</u></b></li>
                <p><b>Explications :</b> Mémorisation de l'état du panneau (ouvert ou fermé) lors de la navigation entre les pages pour ne pas casser ton flux de travail.</p>

                <hr>
                
                <li><b>&#9989;E1 - <u>En cas d'export - version épurée ou pas ?</u></b></li>
                <p><b>Explications :</b> Le choix est fait : l'export génère un site 100% statique (HTML/CSS) pour Nuxit. Le moteur PHP et l'admin restent confinés en local.</p>
                
                <li><b>&#9989;E2 - <u>Nettoyage : suppression des scories lors de l'export</u></b></li>
                <p><b>Explications :</b> Lors de l'envoi vers Nuxit, l'outil fait le ménage automatiquement. On ne livre que le produit fini, léger et sans traces de fabrication.</p>
                
                <li><b>&#9989;E3 - <u>Portabilité : générer un fichier d'archive (.zip)</u></b></li>
                <p><b>Explications :</b> Création d'un package complet prêt à l'emploi pour un déplacement sur n'importe quel serveur par un simple glisser-déposer.</p>

            </ul>
        </article>

        <article class="grid-block">
            <section id="resume-projet">
                <h2>Résumé du Projet : Système de Création Hybride</h2>
                <p>
                    L'objectif est de bâtir un outil de conception web personnel fonctionnant sur un principe de <b>"génération statique"</b>. 
                    Le développement s'appuie sur la puissance d'un moteur dynamique en local pour publier un site invulnérable en ligne.
                </p>

                <hr>

                <div class="bloc-resume">
                    <h3>1. Le Moteur de Contenu (Bloc A)</h3>
                    <p>
                        Le système repose sur une gestion de contenu complète (<b>CRUD</b>). Il permet de créer, modifier et supprimer des articles de manière intuitive. 
                        La gestion des médias est automatisée pour garantir la légèreté du site final.
                    </p>
                </div>

                <div class="bloc-resume">
                    <h3>2. Interface et Expérience de Design (Bloc C)</h3>
                    <p>L'ergonomie est pensée pour le créateur :</p>
                    <ul>
                        <li><b>Confort visuel :</b> Un panneau escamotable permet de masquer l'interface technique.</li>
                        <li><b>Modularité :</b> La construction se fait par blocs réglables sans toucher au code.</li>
                        <li><b>Flux de travail :</b> L'interface mémorise son état pour ne pas interrompre la réflexion.</li>
                    </ul>
                </div>

                <div class="bloc-resume">
                    <h3>3. Stratégie d'Export et Sécurité (Bloc E)</h3>
                    <p>C'est le point fort de l'architecture :</p>
                    <ul>
                        <li><b>Isolation Totale :</b> L'exportation génère un site 100% statique pour le serveur Nuxit.</li>
                        <li><b>Code Propre :</b> Nettoyage automatique des commentaires de développement lors de l'export.</li>
                        <li><b>Portabilité :</b> Génération d'un package .zip prêt à l'emploi.</li>
                    </ul>
                </div>
            </section>
        </article>

    </main>

</div>

<?php 
// 3. Appel du Footer
require_once 'includes/footer.php'; 
?>