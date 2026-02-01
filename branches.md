# Saved Information (Protocoles de Travail)
- [2026-01-27] Relecture systématique des balises de repérage (type ``) avant réponse.
- [2026-01-27] Commentaires HTML/PHP = Éléments structurels intouchables.
- [2026-01-21] Nom : Christophe Millot. Pas de fragments, fichiers complets uniquement. 
- [2026-01-21] Procédure Git push strictement suivie. CSS robuste priorisé sur le SVG.
- [2026-02-01] LOI DE FER : Aucune initiative sur les valeurs (px, vh, classes) sans accord.

---

# Suivi du Projet : Evolution 2026

## Architecture Fondamentale [2026-02-01]

### 1. Structure du Header (Fixe)
- **Fichiers :** `_variables.scss`, `_header.scss`, `header.php`
- **Logique :** Position `fixed`. Hauteur pilotée par `$header-height`.

### 2. Section Hero (Full-Viewport)
- **Fichiers :** `_hero.scss`, `includes/hero.php`
- **Logique :** `height: calc(100vh - $header-height)`. Animation d'entrée par opacité et flou.

### 3. Grille Master et Flux Naturel
- **Fichiers :** `_grid.scss`, `index.php`
- **Logique :** - Isolation du Hero hors grille pour le plein écran.
    - **Zéro Bidouille :** Suppression du `101vh`. Le contenu décide de la hauteur.
    - **Typo :** Lissage `-webkit-font-smoothing` forcé sur `#main` pour la finesse de la police Inter.

---

## Historique des Décisions IA (Discipline de Code)

### [2026-02-01] - Rappel à l'ordre critique
> **ALERTE :** L'IA a tendance à dériver vers des initiatives non sollicitées.
> 1. Interdiction de modifier une valeur non citée par Christophe.
> 2. Interdiction d'ajouter des structures (ex: .grid-block) non présentes dans le source fourni.
> 3. L'argument "C'est le contenu qui fait la hauteur" est le seul dogme.

---

## État des branches & Git
* **main** : Synchronisée avec GitHub (Millot-2023/projet-cms-2026).
* **feat/footer** : Diagnostic noir/blanc activé. Calage sur le flux naturel validé par injection de texte.

