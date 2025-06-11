<?php
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

if (isset($_GET['file'])) {
    $file = htmlspecialchars($_GET['file']);
    
    // Récupération des mots-clés du fichier dans la base de données
    $stmt = $pdo->prepare("SELECT mot FROM mots_cles WHERE fichier = :file");
    $stmt->execute(['file' => $file]);
    $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les mots-clés sous forme JSON
    echo json_encode($keywords);
}
?>
