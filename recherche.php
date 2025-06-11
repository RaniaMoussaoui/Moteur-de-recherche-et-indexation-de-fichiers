<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'indexation';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moteur de recherche</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.3/wordcloud2.min.js"></script>
    <style>
        /* Style général */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
            color: #333;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #2a3f54;
            color: #fff;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2a3f54;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            margin-left: 10px;
            font-size: 16px;
            border: none;
            background-color: #5cb85c;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .results {
            margin-top: 20px;
        }

        .result-item {
            background-color: #e9f5f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #5cb85c;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
        }

        .result-item a {
            color: #2a3f54;
            text-decoration: none;
            font-weight: bold;
        }

        .result-item a:hover {
            text-decoration: underline;
        }

        .result-item p {
            margin-top: 10px;
            color: #555;
        }

        .cloud-icon {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-left: 10px;
        }

        #wordcloud-popup {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border: 1px solid #ccc;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            z-index: 100;
            width: 80%;
            height: 500px;
            max-width: 1000px;
            max-height: 800px;
            overflow: auto;
        }

        #wordcloud-popup #close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        #wordcloud-container {
            width: 100%;
            height: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Moteur de recherche</h1>
    </div>
    <div class="container">
        <form method="GET" action="">
            <input type="text" name="query" placeholder="Entrez un mot" required>
            <button type="submit">Rechercher</button>
        </form>

        <?php
        if (isset($_GET['query'])) {
            $query = htmlspecialchars($_GET['query']); 
            $query = mb_strtolower(trim($query), 'UTF-8'); 

            // Requête SQL pour récupérer et trier les résultats par occurrences
            $stmt = $pdo->prepare("SELECT mot, fichier, occurrences FROM mots_cles WHERE mot = :query ORDER BY occurrences DESC");
            $stmt->execute(['query' => $query]);

            $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($resultats) > 0) {
                echo "<div class='results'>";
                echo "<h2>Résultats pour le mot-clé : '$query'</h2>";
                foreach ($resultats as $resultat) {
                    $fichier = $resultat['fichier'];
                    $occurrences = $resultat['occurrences']; // Récupérer les occurrences
                    $contenu = file_exists($fichier) ? file_get_contents($fichier) : '';
                    $extrait = substr($contenu, 0, 70);
                    $extrait = htmlspecialchars($extrait) . '...';

                    // Affichage du fichier avec les occurrences entre parenthèses
                    echo "<div class='result-item'>";
                    echo "<a href='$fichier' target='_blank'>$fichier ($occurrences)</a>"; // Ajouter les occurrences à côté du fichier
                    echo "<img src='nuage_icon.png' class='cloud-icon' onclick='openWordCloud(\"$fichier\")' />";
                    echo "<p>Extrait : $extrait</p>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<p>Aucun résultat trouvé pour '$query'.</p>";
            }
        }
        ?>
    </div>

    <div id="wordcloud-popup">
        <span id="close-popup" onclick="closeWordCloud()">X</span>
        <div id="wordcloud-container"></div>
    </div>

    <script>
        function openWordCloud(file) {
            document.getElementById("wordcloud-popup").style.display = "block";
            generateWordCloud(file);
        }

        function closeWordCloud() {
            document.getElementById("wordcloud-popup").style.display = "none";
        }

        function generateWordCloud(file) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_keywords.php?file=' + encodeURIComponent(file), true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    var words = JSON.parse(xhr.responseText);
                    var wordList = words.map(function(word) {
                        return [word.mot, Math.floor(Math.random() * 10) + 5];
                    });

                    WordCloud(document.getElementById('wordcloud-container'), {
                        list: wordList,
                        gridSize: 18,
                        weightFactor: 5,
                        color: '#2a3f54',
                        backgroundColor: '#f4f7f9',
                    });
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
