<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $mdp = trim($_POST['mdp'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Vérifier que tous les champs sont remplis
    if (empty($prenom) || empty($nom) || empty($mdp) || empty($role)) {
        die("Tous les champs sont obligatoires.");
    }

    // Connexion à la base de données
    require_once __DIR__ . '/../config/db_connect.php';

    // Requête SQL pour vérifier les identifiants
    $sql = "SELECT id_agent, prenom, nom, mdp, role FROM agents WHERE prenom = ? AND nom = ? AND role = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    // Lier les paramètres et exécuter la requête
    $stmt->bind_param("sss", $prenom, $nom, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si l'utilisateur existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Vérifier le mot de passe
        if ($mdp === $row['mdp']) {
            // Authentification réussie
            $_SESSION['id_agent'] = $row['id_agent'];
            $_SESSION['prenom'] = $row['prenom'];
            $_SESSION['nom'] = $row['nom'];
            $_SESSION['role'] = $row['role'];

            // Redirection en fonction du rôle
            switch ($row['role']) {
                case 'Agent':
                    header("Location: agent_dashboard.html");
                    break;
                case 'Chef':
                    header("Location: chef_dashboard.php");
                    break;
                case 'Magasinier':
                    header("Location: magasinier_dashboard.html");
                    break;
                default:
                    header("Location: agent_dashboard.html");
                    break;
            }
            exit;
        } else {
            echo "Nom d'utilisateur, mot de passe ou rôle incorrect.";
        }
    } else {
        echo "Nom d'utilisateur, mot de passe ou rôle incorrect.";
    }
} else {
    echo "Méthode de requête non autorisée.";
}
?>