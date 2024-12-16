<?php
$conn = new mysqli('localhost', 'root', '', 'academie_de_magie');

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
