# Moteur-de-recherche-et-indexation-de-fichiers
Ce projet est une application web simple développée en **PHP** , permettant :
- aux **utilisateurs** de rechercher des fichiers par mots-clés (comme un moteur de recherche),
- aux **administrateurs** d'indexer, nettoyer et stocker automatiquement les mots-clés issus de fichiers `.txt` dans une base de données.

## Fonctionnalités

### Côté Utilisateur
- Interface type "Google" pour chercher des fichiers par mot-clé.
- Affichage des résultats (nom du fichier + pertinence selon l’occurrence des mots + nuage de mots clés).

### Côté Administrateur
- Formulaire de connexion/création de compte.
- Page d’upload ou sélection de fichiers `.txt`.
- Nettoyage du texte (suppression de ponctuation, mots vides, etc.).
- Extraction automatique des mots-clés et stockage dans la base de données.
- Génération d’un **nuage de mots** via `WordCloud` (Python).
- Option pour visualiser le nuage de mots.

## Technologies utilisées

- **PHP** (version 7+)
- **MySQL** (base de données pour les utilisateurs et mots-clés)
- **HTML/CSS**
- **Python** (optionnel, pour génération du nuage de mots avec la bibliothèque `wordcloud`)
- **JavaScript** (optionnel, pour interactions dynamiques)
