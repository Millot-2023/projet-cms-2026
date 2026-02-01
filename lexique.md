# 📖 Dictionnaire Gemini :

## • Fat finger friendly

Se dit d'une interface utilisateur (boutons, liens, menus) conçue avec des zones cliquables suffisamment larges pour être activées facilement avec le pouce ou l'index, sans erreur de frappe.

L'idée est d'éviter l'effet "gros doigts" où l'on appuie sur deux boutons en même temps ou sur le mauvais lien parce qu'ils sont trop petits ou trop proches.

Les règles d'or (Standard UX)
Pour qu'une interface soit considérée comme Fat finger friendly, elle doit respecter certains critères techniques :

Taille minimale : La zone tactile doit mesurer au moins 44x44 pixels (selon Apple) ou 48x48 pixels (selon Google).

Espacement : Il doit y avoir assez de "vide" (padding/margin) entre deux éléments interactifs.

Accessibilité : C'est essentiel pour les smartphones, mais aussi pour les personnes ayant des troubles de la dextérité.

Exemple concret dans ton code
Au lieu d'un lien texte minuscule :

[Supprimer]

On préférera un bouton robuste en CSS :

[    Supprimer    ] (avec un padding généreux)


# Dictionnaire Markdown

# LEXIQUE MARKDOWN - PROJET-CMS-2026

## 1. Titres (Structure Hiérarchique)
# Titre 1 (Nom du projet uniquement)
## Titre 2 (Sections principales : Architecture, Historique)
### Titre 3 (Sous-sections : Fichiers, Logique)

---

## 2. Listes de Tâches (Task Lists)
* [ ] Tâche à faire (Espace obligatoire après le crochet)
* [x] Tâche terminée (Le 'x' peut être minuscule ou majuscule)

---

## 3. Emphase & Style
*Italique* -> Pour les notes légères.
**Gras** -> Pour les mots-clés et l'emphase.
~~Barré~~    -> Pour les idées ou fonctions abandonnées.

---

## 4. Blocs de Code (Syntax Highlighting)
```scss
// Pour ton SCSS (Coloration syntaxique activée)
.classe { color: $accent; }
```

## 5. Citations & Alertes (Blockquotes)

> **NOTE :**
> Pour que cela fonctionne, il faut impérativement une ligne vide AVANT le chevron `>`.
> Le symbole `>` doit être collé au début de la ligne.

---

## 6. Tableaux (Tables)

| Composant | État | Fichier |
| :--- | :---: | ---: |
| Header | OK | _header.scss |
| Footer | OK | _footer.scss |
---------------------------------------------------------------------------------------



