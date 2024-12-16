<?php
session_start();
include 'db_connection.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>Vous devez vous connecter pour accéder à cette page. <a href='login.php'>Se connecter</a></p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name']);
$user_role = $_SESSION['user_role'];

// Traitement du formulaire d'ajout de sort
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $element = htmlspecialchars($_POST['element']);
    $image = htmlspecialchars($_POST['image']); // Lien de l'image
    $created_by = $user_id;

    // Validation des données
    if (empty($name) || empty($element) || empty($image)) {
        echo "<p style='color: red;'>Tous les champs sont obligatoires.</p>";
    } else {
        // Insérer le sort dans la base de données
        $sql = "INSERT INTO spells (name, element, image, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $element, $image, $created_by);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Sort ajouté avec succès !</p>";
            header("Location: sorts.php");
            exit;
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout du sort : " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Sort</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Ajouter un Sort</h1>
        <a href="sorts.php">Retour au Codex des Sorts</a>
    </header>

    <div class="container">
        <form method="POST" action="add_sort.php">
            <label for="name">Nom du Sort :</label>
            <input type="text" id="name" name="name" placeholder="Entrez le nom du sort" required><br>

            <label for="element">Élément :</label>
            <select id="element" name="element" required>
                <option value="lumière">Lumière</option>
                <option value="eau">Eau</option>
                <option value="air">Air</option>
                <option value="feu">Feu</option>
            </select><br>

            <label for="image">Lien de l'Image :</label>
            <input type="text" id="image" name="image" placeholder="URL de l'image" required><br>

            <button type="submit">Ajouter le Sort</button>
        </form>
    </div>

    <footer>
        <p>© 2024 Académie de Magie. Tous droits réservés.</p>
    </footer>
</body>
</html>
