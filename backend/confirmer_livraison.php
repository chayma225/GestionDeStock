<?php
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_demande = $_POST['id_demande'];

    // Mettre à jour le statut de la demande pour indiquer qu'elle a été livrée
    $sql = "UPDATE demandes SET etat = 'livrée' WHERE id_demande = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_demande);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Supprimer la demande après confirmation de la livraison
        $deleteSql = "DELETE FROM demandes WHERE id_demande = ?";
        $deleteStmt = $conn->prepare($deleteSql);

        if ($deleteStmt === false) {
            die("Erreur de préparation de la requête de suppression : " . $conn->error);
        }

        $deleteStmt->bind_param("i", $id_demande);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows > 0) {
            echo "Demande livrée et supprimée avec succès";
        } else {
            echo "Erreur lors de la suppression de la demande";
        }

        $deleteStmt->close();
    } else {
        echo "Erreur lors de la mise à jour de la demande";
    }

    $stmt->close();
    $conn->close();

    // Redirection après traitement
    header('Location: ../frontend/gestion_demandes.php');
    exit();
}
?>
