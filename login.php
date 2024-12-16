<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur de manière sécurisée
    $sql = "SELECT * FROM users WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Vérification du rôle admin pour redirection
            if ($user['role'] === 'admin') {
                header('Location: index.php');
                echo "<p style='color: red;'>Bonjour admin ben</p>";
            } else {
                header('Location: index.php'); // Redirection pour les utilisateurs normaux
            }
            exit;
        } else {
            echo "<p style='color: red;'>Mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color: red;'>Nom d'utilisateur introuvable.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style_login.css">
</head>
<body>
    <div class="container">
        <h1>Se connecter</h1>
        <form method="POST" action="login.php">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <?php
        if (isset($error_message)) {
            echo "<p>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>

