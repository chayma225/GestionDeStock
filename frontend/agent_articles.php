<?php
session_start();

// Vérification de la catégorie sélectionnée
if (!isset($_GET['categorie'])) {
    header("Location: agent_dashboard.html");
    exit;
}

$categorie = $_GET['categorie'];

// Validation de la catégorie
$categoriesValides = ['Bureautique', 'Informatique'];
if (!in_array($categorie, $categoriesValides)) {
    die("Catégorie invalide.");
}

// Connexion à la base de données
require_once __DIR__ . '/../config/db_connect.php';
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Récupérer les articles de la catégorie sélectionnée
$sql = "SELECT id_article, nom_article, image_data FROM article WHERE categorie = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

$stmt->bind_param("s", $categorie);
$stmt->execute();
$result = $stmt->get_result();

$base_image_path = "http://localhost/mon_application/image/";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - <?php echo htmlspecialchars($categorie); ?></title>
    <link rel="stylesheet" href="styles/agent_articles.css"> <!-- Lien vers le fichier CSS commun -->
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="asset/logo.png" alt="Logo de l'entreprise">
        </div>

        <nav>
            <ul>
                <li><a href="login.html">Accueil</a></li>
                <li><a href="#">À propos</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main>
        <h1>Articles - <?php echo htmlspecialchars($categorie); ?></h1>
        <div class="articles-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="article-card">';
                    echo '<img src="' . $base_image_path . htmlspecialchars($row['image_data']) . '" alt="' . htmlspecialchars($row['nom_article']) . '">';
                    echo '<h3>' . htmlspecialchars($row['nom_article']) . '</h3>';
                    echo '<form action="../backend/demande.php" method="post">';
                    echo '<input type="hidden" name="id_article" value="' . $row['id_article'] . '">';
                    echo '<input type="hidden" name="article" value="' . htmlspecialchars($row['nom_article']) . '">';
                    echo '<label for="quantite">Quantité :</label>';
                    echo '<input type="number" name="quantite" min="1" required>';
                    echo '<button type="submit">Demander</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucun article disponible dans cette catégorie.</p>';
            }
            ?>
        </div>
        <a href="agent_dashboard.html" class="back-link">Retour au tableau de bord</a>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 Votre Entreprise. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>