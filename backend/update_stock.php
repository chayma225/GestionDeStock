<?php
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_article = $_POST['nom_article'];
    $quantity = $_POST['quantity'];

    // Mettre à jour le stock
    $sql = "UPDATE article SET stock = stock + ? WHERE nom_article = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("is", $quantity, $nom_article);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Stock mis à jour avec succès";
    } else {
        echo "Erreur lors de la mise à jour du stock";
    }

    $stmt->close();
    $conn->close();
}
?>
