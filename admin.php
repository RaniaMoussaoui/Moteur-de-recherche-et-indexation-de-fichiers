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

// Vérifiez si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = trim($_POST['username']); // Nom d'utilisateur
    $new_password = trim($_POST['password']); // Mot de passe

    // Vérification : le nom d'utilisateur est-il déjà utilisé ?
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => $new_user]);
    $user_exists = $stmt->fetchColumn();

    if ($user_exists) {
        echo "Erreur : Le nom d'utilisateur '$new_user' existe déjà.";
    } else {
        // Hachage du mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => $new_user,
            'password' => $hashed_password
        ]);

        echo "Utilisateur '$new_user' créé avec succès.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color:#2a3f54;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color:#2a3f54;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Créer un compte</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Créer le compte</button>
        </form>
    </div>
</body>
</html>
