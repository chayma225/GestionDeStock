<?php
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_article = $_POST['nom_article'];
    $quantite = $_POST['quantite'];

    // Vérifier la quantité disponible
    $sql = "SELECT quantite FROM stock WHERE nom_article = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("s", $nom_article);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['quantite'] >= $quantite) {
            // Mettre à jour la quantité en stock
            $sql_update = "UPDATE stock SET quantite = quantite - ? WHERE nom_article = ?";
            $stmt_update = $conn->prepare($sql_update);
            
            if ($stmt_update === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }

            $stmt_update->bind_param("is", $quantite, $nom_article);
            $stmt_update->execute();

            if ($stmt_update->affected_rows > 0) {
                echo "Article prélevé du stock avec succès.";
            } else {
                echo "Erreur lors du prélèvement de l'article du stock.";
            }

            $stmt_update->close();
        } else {
            echo "Quantité insuffisante en stock.";
        }
    } else {
        echo "Article non trouvé.";
    }

    $stmt->close();
    $conn->close();
}
?>
