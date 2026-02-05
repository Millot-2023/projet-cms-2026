<?php
$title = 'carte888';
$category = 'Design';
$date = '05.02.2026';
$summary = '';
$designSystem = array (
  'h1' => 
  array (
    'fontSize' => '64px',
  ),
  'h2' => 
  array (
    'fontSize' => '42px',
  ),
  'h3' => 
  array (
    'fontSize' => '30px',
  ),
  'h4' => 
  array (
    'fontSize' => '24px',
  ),
  'h5' => 
  array (
    'fontSize' => '18px',
  ),
  'p' => 
  array (
    'fontSize' => '18px',
  ),
);
$htmlContent = '<div class="block-container"><div class="delete-block" onclick="this.parentElement.remove()">✕</div><h1 contenteditable="true" onfocus="setTarget(\'h1\')">Titre H1</h1></div><div class="block-container"><div class="delete-block" onclick="this.parentElement.remove()">✕</div><p contenteditable="true" onfocus="setTarget(\'p\')">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p></div><div class="block-container">
            <div class="delete-block" onclick="this.parentElement.remove()">✕</div>
            <div class="float-block">
                <div class="img-placeholder" onclick="setTarget(\'img\', this)" style="float:left; margin:0 20px 10px 0; width:40%; aspect-ratio:16/9; background:#f0f0f0; border:1px solid #ddd; display:flex; align-items:center; justify-content:center; color:#999; font-size:10px; cursor:pointer;">IMAGE</div>
                <p contenteditable="true" onfocus="setTarget(\'p\')">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>
            </div></div>';
?>