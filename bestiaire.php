<?php
session_start();
include 'db_connection.php';

// Vérifiez si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];
    $user_name = htmlspecialchars($_SESSION['user_name']);
} else {
    $user_id = null;
    $user_role = "guest";
    $user_name = "Visiteur";
}

// Traitement du formulaire d'ajout de créature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {
    $name = htmlspecialchars($_POST['name']);
    $type = htmlspecialchars($_POST['type']);
    $description = htmlspecialchars($_POST['description']);
    $image = htmlspecialchars($_POST['image']);

    // Validation des champs
    if (!empty($name) && !empty($type) && !empty($description) && !empty($image)) {
        // Insertion dans la base de données
        $insert_sql = "INSERT INTO creatures (name, type, description, image, created_by) 
                       VALUES ('$name', '$type', '$description', '$image', '$user_id')";
        if ($conn->query($insert_sql)) {
            echo "<p style='color: green;'>Créature ajoutée avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Erreur : " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Tous les champs sont requis.</p>";
    }
}

// Requête SQL pour récupérer toutes les créatures
$sql = "
    SELECT c.creature_id, c.name, c.description, c.type, c.image, c.created_by, u.name AS creator
    FROM creatures c
    JOIN users u ON c.created_by = u.user_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestiaire - Académie de Magie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Le Bestiaire de l'Académie</h1>
        <p>Découvrez les créatures magiques répertoriées dans notre académie</p>
        <a href="index.php">Retour à l'accueil</a>
    </header>

    <div class="container">
        <?php if ($is_logged_in): ?>
            <!-- Formulaire pour ajouter une créature -->
            <h2>Ajouter une nouvelle créature</h2>
            <form method="POST" action="bestiaire.php">
                <label for="name">Nom de la créature :</label>
                <input type="text" id="name" name="name" required><br>

                <label for="type">Type de créature :</label>
                <select id="type" name="type" required>
                    <option value="aquatique">Aquatique</option>
                    <option value="démoniaque">Démoniaque</option>
                    <option value="mort-vivante">Mort-vivante</option>
                    <option value="mi-bête">Mi-bête</option>
                </select><br>

                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="4" required></textarea><br>

                <label for="image">Lien de l'image :</label>
                <input type="text" id="image" name="image" required><br>

                <button type="submit">Ajouter la créature</button>
            </form>
        <?php else: ?>
            <p style="color: red;">Vous devez être connecté pour ajouter une créature.</p>
        <?php endif; ?>

        <h2>Liste des créatures</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Type :</strong> <?= ucfirst(htmlspecialchars($row['type'])); ?></p>
                    <p><?= htmlspecialchars($row['description']); ?></p>
                    <p><strong>Créé par :</strong> <?= htmlspecialchars($row['creator']); ?></p>

                    <!-- Boutons Modifier et Supprimer pour Admin ou le Créateur -->
                    <?php if ($is_logged_in && ($user_role === 'admin' || $user_id == $row['created_by'])): ?>
                        <div class="action-buttons">
                            <a href="edit_creature.php?id=<?= $row['creature_id']; ?>" class="button">Modifier</a>
                            <a href="delete_creature.php?id=<?= $row['creature_id']; ?>" class="button" onclick="return confirm('Voulez-vous vraiment supprimer cette créature ?');">Supprimer</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucune créature n'est disponible pour l'instant.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 2024 Académie de Magie. Tous droits réservés.</p>
    </footer>
</body>
</html>
