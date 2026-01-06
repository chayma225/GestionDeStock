<?php
require_once __DIR__ . '/../config/db_connect.php';

// Fonction pour traiter la demande
function traiter_demande($conn, $id_demande, $nouvel_etat, $quantite_validee = null) {
    // Mise à jour de la demande
    $sql = "UPDATE demandes SET etat = ?, quantite_validee = ? WHERE id_demande = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête (mise à jour de la demande) : " . $conn->error);
    }

    $stmt->bind_param('sii', $nouvel_etat, $quantite_validee, $id_demande);

    if (!$stmt->execute()) {
        die("Erreur d'exécution de la requête (mise à jour de la demande) : " . $stmt->error);
    }

    // Si la demande est prête à livrer, réserver la quantité en stock
    if ($nouvel_etat == 'Prête à livrer') {
        $sql_stock = "UPDATE article a
                      JOIN demandes d ON a.nom_article = d.nom_article
                      SET a.quantite_reservee = a.quantite_reservee + ?
                      WHERE d.id_demande = ?";
        $stmt_stock = $conn->prepare($sql_stock);

        if ($stmt_stock === false) {
            die("Erreur de préparation de la requête (réservation de stock) : " . $conn->error);
        }

        $stmt_stock->bind_param('ii', $quantite_validee, $id_demande);

        if (!$stmt_stock->execute()) {
            die("Erreur d'exécution de la requête (réservation de stock) : " . $stmt_stock->error);
        }
    }

    return true;
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_demande = $_POST['id_demande'];
        $action = $_POST['action'];
        $quantite_validee = $_POST['quantite_validee'] ?? null;

        if ($action == 'valider_partiellement') {
            $nouvel_etat = 'Prête à livrer';
        } elseif  ($action == 'valider') {
                $nouvel_etat = 'Prête à livrer';
        } elseif ($action == 'refuser') {
            $nouvel_etat = 'Refusée';
        } else {
            throw new Exception("Action non reconnue.");
        }

        $max_retries = 3;
        $retry_count = 0;
        $success = false;

        while ($retry_count < $max_retries && !$success) {
            try {
                $conn->begin_transaction();
                $success = traiter_demande($conn, $id_demande, $nouvel_etat, $quantite_validee);
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                $retry_count++;
                if ($retry_count == $max_retries) {
                    throw $e;
                }
            }
        }

        if ($success) {
            echo "<script>alert('Demande mise à jour avec succès.'); window.location.href = '../frontend/chef_dashboard.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de la demande.'); window.location.href = '../frontend/chef_dashboard.php';</script>";
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Erreur : " . $e->getMessage() . "'); window.location.href = '../frontend/chef_dashboard.php';</script>";
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
