// Fonction pour traiter les fichiers et extraire les mots
function indexerFichiers($fichiers, $mots_vides, $pdo) {
    $resultats = [];

    foreach ($fichiers as $fichier) {
        $extension = pathinfo($fichier, PATHINFO_EXTENSION);

        // Lire le contenu selon le type de fichier
        if ($extension === 'txt') {
            $contenu = file_get_contents($fichier);
        } else {
            continue; // Ignorer les fichiers non supportés
        }

        // Remplacement des sauts de ligne et espaces multiples par un espace unique
        $contenu = preg_replace("/\s+/", " ", $contenu);

        // Nettoyage du texte (suppression des caractères indésirables)
        $texte_sans_ponctuation = preg_replace("/[^a-zA-ZÀ-ÿ0-9\s]/u", " ", $contenu);

        // Découpage en mots
        $mots = explode(" ", $texte_sans_ponctuation);

        foreach ($mots as $mot) {
            // Conversion en minuscule et suppression des espaces en trop
            $mot = mb_strtolower(trim($mot), 'UTF-8');

            // Vérification de la longueur et exclusion des mots vides
            if (strlen($mot) > 2 && !in_array($mot, $mots_vides)) {
                if (!isset($resultats[$fichier][$mot])) {
                    $resultats[$fichier][$mot] = 0;
                }
                $resultats[$fichier][$mot]++;
            }
        }
    }

    // Insertion des résultats dans la base de données (enregistrement du nom du fichier et des occurrences)
    $stmt = $pdo->prepare("
        INSERT INTO mots_cles (mot, fichier, occurrences) 
        VALUES (:mot, :fichier, :occurrences) 
        ON DUPLICATE KEY UPDATE occurrences = occurrences + :occurrences
    ");

    foreach ($resultats as $fichier => $mots) {
        foreach ($mots as $mot => $count) {
            // On extrait uniquement le nom du fichier
            $nom_fichier = basename($fichier);

            $stmt->execute([
                'mot' => $mot,
                'fichier' => $nom_fichier,  // Utilisation du nom du fichier sans son chemin
                'occurrences' => $count
            ]);
        }
    }

    return $resultats;
}
