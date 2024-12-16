<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Ajout de l'utilisateur dans la base de données
    $sql = "INSERT INTO users (name, password, role, elements_id) VALUES (?, ?, 'user', 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "<p>Inscription réussie. <a href='login.php'>Connectez-vous ici</a>.</p>";
    } else {
        echo "<p style='color: red;'>Erreur lors de l'inscription : " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>S'inscrire</h1>
    <form method="POST" action="register.php">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
