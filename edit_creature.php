<?php
session_start();
include 'db_connection.php';

// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Vérifier si l'ID de la créature est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de créature invalide.");
}

$creature_id = $_GET['id'];

// Récupérer les informations de la créature
$sql = "SELECT * FROM creatures WHERE creature_id = $creature_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Créature non trouvée.");
}

$creature = $result->fetch_assoc();

// Vérification des permissions (Admin ou Créateur)
if ($user_role !== 'admin' && $creature['created_by'] != $user_id) {
    die("Vous n'avez pas la permission de modifier cette créature.");
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Échapper les données pour éviter les erreurs SQL
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    // Mise à jour dans la base de données
    $update_sql = "
        UPDATE creatures 
        SET name = '$name', 
            type = '$type', 
            description = '$description', 
            image = '$image' 
        WHERE creature_id = $creature_id
    ";

    if ($conn->query($update_sql)) {
        echo "<p style='color: green;'>Créature mise à jour avec succès !</p>";
        // Redirection après la mise à jour
        header("Location: bestiaire.php");
        exit;
    } else {
        echo "<p style='color: red;'>Erreur lors de la mise à jour : " . $conn->error . "</p>";
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Créature</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_edit_creature.css">
</head>
<body>
    <header>
        <h1>Modifier une Créature</h1>
        <a href="bestiaire.php">Retour au Bestiaire</a>
    </header>

    <div class="container">
        <form method="POST" action="edit_creature.php?id=<?= htmlspecialchars($creature_id); ?>">
            <label for="name">Nom de la Créature :</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($creature['name']); ?>" required><br>

            <label for="type">Type :</label>
            <select id="type" name="type" required>
                <option value="aquatique" <?= $creature['type'] === 'aquatique' ? 'selected' : ''; ?>>Aquatique</option>
                <option value="démoniaque" <?= $creature['type'] === 'démoniaque' ? 'selected' : ''; ?>>Démoniaque</option>
                <option value="mort-vivante" <?= $creature['type'] === 'mort-vivante' ? 'selected' : ''; ?>>Mort-vivante</option>
                <option value="mi-bête" <?= $creature['type'] === 'mi-bête' ? 'selected' : ''; ?>>Mi-bête</option>
            </select><br>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($creature['description']); ?></textarea><br>

            <label for="image">Lien de l'Image :</label>
            <input type="text" id="image" name="image" value="<?= htmlspecialchars($creature['image']); ?>" required><br>

            <button type="submit">Enregistrer les Modifications</button>
        </form>
    </div>

    <footer>
        <p>© 2024 Académie de Magie. Tous droits réservés.</p>
    </footer>
</body>
</html>
