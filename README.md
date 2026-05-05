# 📑 Audit Qualité du Code - Projet : Mon Pti Budjet
**Date de l'audit :** 05 Mai 2026
**Groupe :** [Votre Nom / Groupe]

---

## 🌐 1. HTML — Structure & Sémantique
| Critère | État | Note / Exemple de code |
| :--- | :---: | :--- |
| **Structure sémantique** | ⚠️ Partiel | Utilisation de `<div>` pour les wrappers de formulaires au lieu de `<section>` ou `<article>` |
| **Pas de style inline** | ✅ Conforme | Le design est géré via `style_2.css`, aucun attribut `style="..."` n'a été détecte. |
| **Pas de duplication** | ⚠️ Partiel | Les structures HTML des formulaires d'auth sont très répétitives entre `register.php` et `login.php`. |
| **Attributs `alt` sur images** | ✅ Conforme | Les icônes et logos possèdent des descriptions alternatives. |
| **Hiérarchie des titres (H1-Hn)**| ⚠️ À corriger | Présence de plusieurs `<h1>` dans le tableau de bord; à remplacer par des `<h2>`. |
| **Formulaires (Label/Input)** | ✅ Conforme | Utilisation correcte des attributs `for` et `id` pour l'accessibilité. |
| **Pas de balises obsolètes** | ✅ Conforme | Utilisation de PHP moderne et de balises HTML5. |
| **Fichiers liés (Ordre)** | ✅ Conforme | Scripts JS placés avant la fermeture du `</body>`. |

---

## 🎨 2. CSS — Design & Maintenance
| Critère | État | Note / Exemple de code |
| :--- | :---: | :--- |
| **Pas de règles en double** | ⚠️ À corriger | Plusieurs classes répètent les mêmes propriétés `border-radius` et `box-shadow`. |
| **Variables CSS** | ❌ À corriger | Les couleurs (bleu, gris) sont codées en dur (ex: `#5555f8`) au lieu d'utiliser `:root`. |
| **Responsive Design** | ✅ Conforme | Utilisation de Flexbox pour adapter les formulaires sur mobile. |
| **Nommage des classes** | ✅ Conforme | Noms explicites : `.formulaire-connexion`, `.warning`, `.auth-container`. |
| **Utilisation de Flex/Grid** | ✅ Conforme | Mise en page moderne évitant les `float`. |
| **Commentaires de section** | ❌ À corriger | Manque de séparateurs clairs dans le fichier CSS pour distinguer l'UI globale des pages. |

---

## 🐘 3. PHP — Sécurité & Logique
| Critère | État | Note / Exemple de code |
| :--- | :---: | :--- |
| **Requêtes préparées** | ✅ Conforme | Utilisation systématique de `$con->prepare()` contre les injections SQL |
| **Séparation Logique/Vue** | ⚠️ Partiel | Les traitements POST sont souvent en haut du fichier HTML. À extraire vers des contrôleurs. |
| **Transactions SQL** | ❌ À corriger | Pas de `beginTransaction()` lors de la création de compte ou reset password. |
| **Gestion des erreurs** | ⚠️ Partiel | Feedback via `$_SESSION['status']` présent, mais manque de blocs `try/catch`. |
| **Validation des données** | ✅ Conforme | Usage de `password_hash()` et vérification des champs vides[cite: 23]. |
| **Utilisation de include/require**| ✅ Conforme | Factorisation réussie du `header.php`, `footer.php` et `connexion.php`. |
| **Pas d'erreurs en prod** | ❌ À corriger | `display_errors` n'est pas encore désactivé via un fichier de config global. |

---

## ⚡ 4. JavaScript — Interactivité
| Critère | État | Note / Exemple de code |
| :--- | :---: | :--- |
| **const et let vs var** | ✅ Conforme | Utilisation exclusive de `const` et `let` dans les scripts modernes. |
| **Cache des sélections DOM** | ✅ Conforme | Les éléments comme les formulaires sont stockés dans des variables. |
| **Gestion des événements** | ✅ Conforme | Utilisation de `addEventListener` plutôt que des attributs `onclick` HTML. |
| **Pas de code JS dans HTML** | ✅ Conforme | Séparation propre dans des fichiers `.js` externes. |
| **Gestion erreurs Fetch** | ⚠️ Partiel | Manque de vérification de `response.ok` sur certains appels API. |

---

## 📅 Plan de mise en conformité (Roadmap)

1. **Priorité 1 (Sécurité) :** Implémenter les **Transactions SQL** dans `reset-password.php` et `register.php` pour éviter les données orphelines.
2. **Priorité 2 (Maintenance) :** Créer un fichier `:root` en CSS avec des **variables** pour les couleurs et fonts.
3. **Priorité 3 (Accessibilité) :** Corriger la **sémantique des titres** (H1 unique par page) et ajouter des balises `<section>`.
4. **Priorité 4 (Nettoyage) :** Supprimer tous les `console.log` de debug et masquer les erreurs PHP en production.


