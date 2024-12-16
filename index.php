<?php
session_start();
include 'db_connection.php';

// Vérifiez si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']); // Vérifie si l'utilisateur est connecté
if ($is_logged_in) {
    // Récupérer les informations utilisateur si connecté
    $user_id = $_SESSION['user_id'];
    $user_name = htmlspecialchars($_SESSION['user_name']);
    $user_role = htmlspecialchars($_SESSION['user_role']);
} else {
    // Valeurs par défaut pour les utilisateurs non connectés
    $user_id = null;
    $user_name = "Visiteur";
    $user_role = "guest";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Académie de Magie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bienvenue à l'Académie de Magie</h1>
        <p>Explorez le bestiaire et le codex des sorts magiques</p>
        <?php if ($is_logged_in): ?>
            <p>Bienvenue, <?= $user_name; ?> | <a href="logout.php">Se déconnecter</a></p>
        <?php else: ?>
            <p><a href="login.php">Se connecter</a> ou <a href="register.php">S'inscrire</a></p>
        <?php endif; ?>
    </header>

    <div class="container">
        <h2>Explorez les sections</h2>

        <!-- Conteneur flex pour les images -->
        <div class="image-container-wrapper">
            <!-- Image pour le Bestiaire -->
            <div class="image-container">
                <a href="bestiaire.php">
                    <img src="img/aquatique.png" alt="Bestiaire">
                </a>
                <p>Monstre</p>
            </div>

            <!-- Image pour les Sorts -->
            <div class="image-container">
                <a href="sorts.php">
                    <img src="img/livre_de_sort.png" alt="Codex des Sorts">
                </a>
                <p>sort</p>
            </div>
        </div>

        <h2>Bestiaire</h2>
        <?php
        $sql = "SELECT c.creature_id, c.name, c.type, c.description, c.image, u.name AS creator 
                FROM creatures c
                JOIN users u ON c.created_by = u.user_id";
        $result = $conn->query($sql);

        if (!$result) {
            echo "<p style='color: red;'>Erreur dans la requête SQL : " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
                echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                echo '<p><strong>Type :</strong> ' . ucfirst(htmlspecialchars($row["type"])) . '</p>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '<p><strong>Créé par :</strong> ' . htmlspecialchars($row["creator"]) . '</p>';

                // Afficher les options de suppression/modification uniquement pour les admins
                if ($is_logged_in && $user_role === 'admin') {
                    echo '<a href="delete_creature.php?id=' . $row['creature_id'] . '">Supprimer</a>';
                }
                echo '</div>';
            }
        } else {
            echo "<p>Aucune créature n'a été ajoutée.</p>";
        }
        ?>
    </div>
</body>
</html>
