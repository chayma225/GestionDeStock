<?php
session_start();

// Vérifier que l'agent est connecté
if (!isset($_SESSION['id_agent'])) {
    header("Location: ../frontend/login.php"); // Rediriger vers la page de connexion
    exit;
}

$id_agent = $_SESSION['id_agent'];

// Connexion à la base de données
require_once __DIR__ . '/../config/db_connect.php';

if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id_article = $_POST['id_article'] ?? '';
    $nom_article = $_POST['nom_article'] ?? '';
    $quantite = $_POST['quantite'] ?? '';

    // Valider uniquement la quantité
    if (empty($quantite) || $quantite < 1) {
        die("La quantité est obligatoire et doit être supérieure à 0.");
    }

    // Vérifier la quantité disponible dans la table `article`
    $sql = "SELECT stock, nom_article FROM article WHERE id_article = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("i", $id_article);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stock_disponible = $row['stock']; // Récupérer le stock disponible
        $nom_article = $row['nom_article']; // Récupérer le nom de l'article

        // Vérifier si la quantité demandée est disponible
        if ($quantite <= $stock_disponible) {
            // Enregistrer la demande dans la base de données
            $sql = "INSERT INTO demandes (id_article, id_agent, nom_article, quantite, etat, date_demande) VALUES (?, ?, ?, ?, 'En attente', NOW())";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }

            $stmt->bind_param("iisi", $id_article, $id_agent, $nom_article, $quantite);

            if ($stmt->execute()) {
                // Afficher une alerte pour confirmer l'enregistrement
                echo "<script>
                    alert('Votre demande a été enregistrée avec succès.');
                    window.location.href = '../frontend/agent_dashboard.html'; // Rediriger après la fermeture de l'alerte
                </script>";
            } else {
                // Afficher une alerte en cas d'erreur
                echo "<script>
                    alert('Erreur lors de l\'enregistrement de la demande.');
                </script>";
            }
        } else {
            // Afficher une alerte si la quantité demandée n'est pas disponible
            echo "<script>
                alert('La quantité demandée n\'est pas disponible. Stock disponible pour \"' + '" . $nom_article . "' + '\" : ' + '" . $stock_disponible . "');
            </script>";
        }
    } else {
        // Afficher une alerte si l'article n'est pas trouvé
        echo "<script>
            alert('L\'article demandé n\'existe pas.');
        </script>";
    }
} else {
    // Afficher une alerte si la méthode de requête n'est pas autorisée
    echo "<script>
        alert('Méthode de requête non autorisée.');
    </script>";
}

$stmt->close();
$conn->close();
?>