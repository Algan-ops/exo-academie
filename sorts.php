<?php
session_start();
include 'db_connection.php';

// Vérifiez si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']); // Vérifie si l'utilisateur est connecté
if ($is_logged_in) {
    $user_name = htmlspecialchars($_SESSION['user_name']);
} else {
    $user_name = "Visiteur";
}

// Requête SQL pour récupérer les sorts
$sql = "
    SELECT s.name, s.element, u.name AS creator
    FROM spells s
    JOIN users u ON s.created_by = u.user_id
";

$result = $conn->query($sql);
?>
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="add_sort.php" class="button">Ajouter un Sort</a>
<?php endif; ?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex des Sorts - Académie de Magie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Codex des Sorts de l'Académie</h1>
        <p>Découvrez les sorts magiques répertoriés dans notre académie</p>
        <a href="index.php">Retour à l'accueil</a>
    </header>

    <div class="container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Élément :</strong> <?= ucfirst(htmlspecialchars($row['element'])); ?></p>
                    <p><strong>Créé par :</strong> <?= htmlspecialchars($row['creator']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucun sort n'a été ajouté pour l'instant.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 2024 Académie de Magie. Tous droits réservés.</p>
    </footer>
</body>
</html>
