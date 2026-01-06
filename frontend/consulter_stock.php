<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter le Stock</title>
    <link rel="stylesheet" href="styles/consulter_stock.css">
    <script>
        function showQuantityInput(nomArticle) {
            var inputDiv = document.getElementById('input-' + nomArticle);
            inputDiv.style.display = 'block';
        }

        function updateStock(nomArticle) {
            var quantity = document.getElementById('quantity-' + nomArticle).value;
            // Ajax request to update stock
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../backend/update_stock.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Stock mis à jour !");
                    location.reload();
                }
            };
            xhr.send("nom_article=" + nomArticle + "&quantity=" + quantity);
        }
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="asset/logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="login.html">Accueil</a></li>
                    <li><a href="gestion_demandes.php">Demandes</a></li>
                    <li><a href="consulter_stock.php">Stock</a></li>
                    <li><a href="../backend/logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h1>Consulter le Stock</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom de l'article</th>
                    <th>Quantité en stock</th>
                    <th>Quantité réservée</th>
                    <th>Stock disponible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once __DIR__ . '/../config/db_connect.php';

                $sql = "SELECT nom_article, stock, quantite_reservee, (stock - quantite_reservee) AS stock_disponible FROM article";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['nom_article']}</td>
                                <td>{$row['stock']}</td>
                                <td>{$row['quantite_reservee']}</td>
                                <td>{$row['stock_disponible']}</td>
                                <td>
                                    <button onclick=\"showQuantityInput('{$row['nom_article']}')\" class='btn btn-secondary'>Ajouter au Stock</button>
                                    <div id='input-{$row['nom_article']}' class='quantity-container' style='display: none;'>
                                        <input type='number' id='quantity-{$row['nom_article']}' placeholder='Quantité'>
                                        <button onclick=\"updateStock('{$row['nom_article']}')\" class='btn btn-primary'>Mettre à jour</button>
                                    </div>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucun article en stock.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2023 Votre Entreprise. Tous droits réservés.</p>
    </footer>
</body>
</html>
