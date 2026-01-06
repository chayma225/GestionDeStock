<?php
require_once __DIR__ . '/../config/db_connect.php';

// Fonction pour confirmer la livraison et mettre à jour le stock
function confirmer_livraison($conn, $id_demande) {
    $sql = "SELECT d.id_article, d.quantite_validee, a.nom_article 
            FROM demandes d 
            JOIN article a ON d.nom_article = a.nom_article 
            WHERE d.id_demande = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_demande);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_article = $row['id_article'];
        $quantite_validee = $row['quantite_validee'];

        // Mettre à jour la quantité en stock et annuler la réservation
        $sql_stock = "UPDATE article SET stock = stock - ?, quantite_reservee = quantite_reservee - ? WHERE id = ?";
        $stmt_stock = $conn->prepare($sql_stock);

        if ($stmt_stock === false) {
            die("Erreur de préparation de la requête de stock : " . $conn->error);
        }

        $stmt_stock->bind_param('iii', $quantite_validee, $quantite_validee, $id_article);

        if ($stmt_stock->execute()) {
            // Enregistrer le mouvement de stock
            $sql_mouvement = "INSERT INTO mouvements_stock (id_article, type_mouvement, quantite) VALUES (?, 'Livraison', ?)";
            $stmt_mouvement = $conn->prepare($sql_mouvement);

            if ($stmt_mouvement === false) {
                die("Erreur de préparation de la requête de mouvement : " . $conn->error);
            }

            $stmt_mouvement->bind_param("ii", $id_article, $quantite_validee);
            $stmt_mouvement->execute();

            // Mettre à jour l'état de la demande à 'Livrée'
            $sql_demande = "UPDATE demandes SET etat = 'Livrée' WHERE id_demande = ?";
            $stmt_demande = $conn->prepare($sql_demande);

            if ($stmt_demande === false) {
                die("Erreur de préparation de la requête de demande : " . $conn->error);
            }

            $stmt_demande->bind_param('i', $id_demande);
            $stmt_demande->execute();

            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_demande = $_POST['id_demande'];

        if (confirmer_livraison($conn, $id_demande)) {
            echo "<script>alert('Livraison confirmée et stock mis à jour avec succès.'); window.location.href = '../frontend/gestion_demandes.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la confirmation de la livraison.'); window.location.href = '../frontend/gestion_demandes.php';</script>";
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Erreur : " . $e->getMessage() . "'); window.location.href = '../frontend/gestion_demandes.php';</script>";
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
