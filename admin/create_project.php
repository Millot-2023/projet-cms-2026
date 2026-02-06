<?php
/** * Script de création de nouveau projet - Studio CMS 
 * @author: Christophe Millot
 */

$projectID = 'project-' . time(); 
// Utilisation de realpath pour garantir que PHP trouve bien le dossier parent
$targetDir = dirname(__DIR__) . '/content/' . $projectID;

if (!file_exists($targetDir)) {
    // Le 0755 est souvent préférable au 0777 sur certains serveurs sécurisés
    if (mkdir($targetDir, 0755, true)) {
        
        $defaultData = "<?php
/** Fichier généré par Studio CMS **/

\$title = 'Nouveau Projet';
\$cover = '';
\$category = 'Design';
\$date = '" . date('d.m.Y') . "';
\$summary = '';
\$designSystem = array (
  'h1' => array ('fontSize' => '64px'),
  'h2' => array ('fontSize' => '42px'),
  'h3' => array ('fontSize' => '30px'),
  'h4' => array ('fontSize' => '24px'),
  'h5' => array ('fontSize' => '18px'),
  'p' => array ('fontSize' => '18px'),
);
\$htmlContent = '<div class=\"block-container\"><p>Commencez à rédiger votre contenu ici...</p></div>';
?>";

        file_put_contents($targetDir . '/data.php', $defaultData);
    }
}

// Redirection vers l'éditeur
header('Location: editor.php?project=' . $projectID);
exit;
?>