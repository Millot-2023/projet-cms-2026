# 🛠️ CMS-2026 : DOCUMENTATION UNIQUE & SYNTHÈSE GLOBALE

## 1. SUIVI D'ÉVOLUTION (ex: branches.md)

### Vision Stratégique [2026-02-02]
- **Concept :** CMS Dynamique Local (XAMPP) -> Export Statique Production (Nuxit).
- **Surface d'Attaque :** Nulle (Fichiers .php de données convertis ou sécurisés).
- **Architecture :** Article > Sections (Grid-block). Système de stockage : Flat-file (data.php).

### État des Blocs (Cahier des Charges)
- **[A] Contenu :** ✅ Validé (CRUD dossiers/fichiers opérationnel).
- **[B] Sécurité :** ✅ Validé (Filtrage IP locale + Verrouillage Sidebar).
- **[C] Interface :** ✅ Validé (Bouton Quitter fixe, Sidebar noire #000000, Cockpit stabilisé).
- **[D] Rendu :** ✅ Validé (Grille Militaire 220px, Isolation .editor-grid, Extraction images physiques).
- **[E] Export :** ⚪ En attente.

### Historique des Branches (Merged)
- **feat/core-structure** : Génération auto des projets et moteur de sauvegarde.
- **stabilite-editeur-2026** : Finalisation ergonomie, bouton QUITTER fixe, nettoyage fichiers temporaires.
- **feat/ui-refinement** : Grille Militaire (220px), `object-fit: cover`, isolation structurelle.
- **feat/trash-and-clean** : Système de suppression récursive (`admin/delete.php`), extraction du Base64 vers fichiers réels.

---

## 2. DICTIONNAIRE & PROTOCOLES (ex: lexique.md)

### Concepts UX/UI
- **Fat finger friendly :** Interface conçue avec des zones cliquables larges (min 44x44px) pour éviter les erreurs sur écran tactile.
- **Grille Militaire :** Verrouillage strict de l'affichage des cards à 220px de hauteur pour une uniformité totale.
- **Local-First Design :** Construction sur machine locale (XAMPP) avant déploiement.

### Concepts Sécurité
- **Surface d'Attaque Minimale :** Limitation des vecteurs d'attaque par l'usage de fichiers statiques en production.
- **Flat-file CMS :** Stockage des données dans des fichiers individuels (data.php) sans base de données SQL.
- **Slug :** Identifiant unique du projet correspondant au nom de son dossier dans `content/`.

---

## 3. PROTOCOLES DE DÉPLOIEMENT GIT (ex: PUSH.md & INFOS-GIT.md)

### Procédure de Synchronisation Standard
1. **État des modifications :** `git status`
2. **Indexation :** `git add .`
3. **Commit :** `git commit -m "TYPE: Description du changement"`
4. **Push :** `git push origin main`

### Notes de maintenance Git
- Toujours vérifier que l'on est sur la branche `main` pour les versions définitives.
- Utiliser des messages de commit explicites (ex: "Feat:...", "Fix:...", "Docs:...").

---

## 4. DISCIPLINE DE CODE & DÉCISIONS IA
- **[2026-02-06] :** Sidebar gauche impérativement à `#000000`.
- **[2026-02-06] :** Interdiction de fragmenter les fichiers (envoi de 100% du code).
- **[2026-02-07] :** Priorité au CSS robuste sur le SVG pour l'architecture.
- **[2026-02-07] :** Suppression systématique du Base64 dans `data.php` au profit de fichiers physiques dans le dossier projet.

---

## 5. RAPPELS SYNTAXE MARKDOWN
- `#` : Titre 1 (Unique)
- `##` : Titre 2 (Sections)
- `[ ] / [x]` : Checklists de tâches
- `> ` : Citations ou alertes importantes
- ```langage : Blocs de code avec coloration syntaxique

---

## 6. AUDIT DE STRUCTURE & VÉRIFICATIONS CRITIQUES
- **Flux Data** : Interdiction de réinjecter du Base64. Toute modification de `save.php` doit garantir l'extraction JPG/PNG.
- **Identité** : Le nom du dossier projet dans `/content` FAIT LOI. Ne jamais renommer manuellement sans mettre à jour le lien interne.
- **Interface** : Respect strict du noir `#000000` pour la sidebar. C'est un repère visuel de sécurité (Admin vs Public).
- **Nettoyage** : Avant chaque `git push`, vérifier l'absence de fichiers `.tmp` ou `copy.php`.

---

## 7. GESTION DES ACTIFS & INTÉGRITÉ (LOGIQUE SYSTÈME)
- **Chemins Absolus vs Relatifs** : En administration, toujours privilégier les chemins relatifs au dossier `admin/` pour garantir la portabilité du CMS.
- **Protocole de Sauvegarde (Fail-Safe)** : Toute écriture dans `data.php` doit d'abord valider l'existence du dossier `/content/[slug]`. Si l'extraction image échoue, le `data.php` ne doit pas être tronqué.
- **Autonomie CSS** : Priorité absolue aux styles encapsulés. Aucune dépendance externe (CDN) n'est autorisée afin de garantir le fonctionnement 100% Hors-Ligne (XAMPP).
- **Nettoyage Automatique** : Tout fichier média orphelin (non référencé dans le `data.php` final) doit être signalé lors de l'audit pour suppression manuelle, évitant ainsi le gonflement inutile du dépôt.