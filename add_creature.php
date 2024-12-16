<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $image = 'img/' . $_FILES['image']['name'];
    $created_by = $_SESSION['user_id']; // L'utilisateur connecté

    // Déplacer le fichier image vers le dossier "img"
    if (move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
        $sql = "INSERT INTO creatures (name, type, description, image, created_by) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $type, $description, $image, $created_by);

        if ($stmt->execute()) {
            echo "<p>Créature ajoutée avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout : " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Erreur lors du téléchargement de l'image.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Créature</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ajouter une Créature</h1>
    <form method="POST" action="add_creature.php" enctype="multipart/form-data">
        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" required><br>

        <label for="type">Type :</label>
        <select id="type" name="type" required>
            <option value="aquatique">Aquatique</option>
            <option value="démoniaque">Démoniaque</option>
            <option value="mort-vivante">Mort-vivante</option>
            <option value="mi-bête">Mi-bête</option>
        </select><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="image">Image :</label>
        <input type="file" id="image" name="image" accept="image/*" required><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
