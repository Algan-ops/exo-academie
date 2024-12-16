<?php
session_start();
include 'db_connection.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>Vous devez vous connecter pour accéder à cette page. <a href='login.php'>Se connecter</a></p>";
    exit;
}

// Récupération des informations de session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Vérifiez si l'ID de la créature est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color: red;'>ID de créature invalide.</p>";
    exit;
}

$creature_id = $_GET['id'];

// Vérifiez si la créature existe et que l'utilisateur a les permissions
$sql = "SELECT created_by FROM creatures WHERE creature_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $creature_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color: red;'>Créature non trouvée.</p>";
    exit;
}

$creature = $result->fetch_assoc();

// Vérification des permissions : seul l'admin ou le créateur peut supprimer
if ($user_role !== 'admin' && $creature['created_by'] != $user_id) {
    echo "<p style='color: red;'>Vous n'avez pas la permission de supprimer cette créature.</p>";
    exit;
}

// Suppression de la créature
$delete_sql = "DELETE FROM creatures WHERE creature_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $creature_id);

if ($delete_stmt->execute()) {
    echo "<p style='color: green;'>Créature supprimée avec succès.</p>";
    header("Location: bestiaire.php");
    exit;
} else {
    echo "<p style='color: red;'>Erreur lors de la suppression : " . $conn->error . "</p>";
}

// Fermer la connexion
$conn->close();
?>
