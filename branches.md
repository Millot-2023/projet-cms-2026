# Suivi du Projet : Evolution (Système Hybride)

## Vision Stratégique [2026-02-02]
- **Concept :** CMS Dynamique Local (XAMPP) -> Export Statique Production (Nuxit).
- **Surface d'Attaque :** Nulle (Fichiers .php de données convertis ou sécurisés).
- **Architecture :** Article > Sections (Grid-block). Système de stockage : Flat-file (data.php).

## État des Blocs (Cahier des Charges)
- **[A] Contenu :** ✅ Validé (CRUD dossiers/fichiers opérationnel).
- **[B] Sécurité :** ✅ Validé (Filtrage IP locale + Verrouillage Sidebar).
- **[C] Interface :** ✅ Validé (Cockpit avec Poubelle, Sidebar noire #000000).
- **[D] Rendu :** ✅ Validé (Grille 220px, Images extraites du Base64).
- **[E] Export :** ⚪ En attente.

---

## Plan de Développement (Branches Git)

### 1. Branche : `feat/core-structure` [TERMINE / MERGED]
- **Objectif :** Génération auto des projets et moteur de sauvegarde.

### 2. Branche : `stabilite-editeur-2026` [TERMINE / MERGED]
- **Objectif :** Finalisation de l'ergonomie de l'éditeur.

### 3. Branche : `feat/ui-refinement` [TERMINE / MERGED]
- **Objectif :** Identité visuelle des Cards et stabilisation de la vue Article.

### 4. Branche : `feat/trash-and-clean` [TERMINE / MERGED]
- **Objectif :** Allègement du poids des données et gestion de la suppression.
- **Résultat :** - **Extraction Physique :** Conversion automatique du Base64 en fichiers images réels lors de la sauvegarde (`admin/save.php`).
    - **Système Poubelle :** Ajout d'une croix de suppression sur les cartes du cockpit avec confirmation JS de sécurité.
    - **Nettoyage Chirurgical :** Création de `admin/delete.php` pour l'effacement complet des dossiers projets (données + images).

---

## Historique des Décisions IA (Discipline de Code)
- **[2026-01-30] :** Nommage du fichier de suivi "branches.md".
- **[2026-02-02] :** Validation du moteur de sauvegarde.
- **[2026-02-06] :** Stabilisation du cockpit. Respect strict de la sidebar noire (#000000). Interdiction formelle de fragmenter les fichiers PHP.
- **[2026-02-07] :** **Optimisation de la Data :** Abandon du stockage Base64 au profit de fichiers physiques pour garantir la légèreté de `data.php` et la rapidité d'affichage.
- **[2026-02-07] :** **Mise en place de la démolition contrôlée :** Intégration du système de suppression récursive des dossiers de contenu.