<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes</title>
    <link rel="stylesheet" href="styles/gestion_demandes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function removeRow(rowId) {
            var row = document.getElementById(rowId);
            row.parentNode.removeChild(row);
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
        <h1>Gestion des Demandes</h1>
        <table>
            <thead>
                <tr>
                    <th>ID Demande</th>
                    <th>Nom de l'article</th>
                    <th>Quantité</th>
                    <th>Agent</th>
                    <th>Date de demande</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once __DIR__ . '/../config/db_connect.php';

                $sql = "SELECT d.id_demande, d.nom_article, d.quantite, a.prenom AS prenom_agent, a.nom AS nom_agent, d.date_demande
                        FROM demandes d
                        JOIN agents a ON d.id_agent = a.id_agent
                        WHERE d.etat = ''";
                $result = $conn->query($sql);

                // Vérifier si la requête SQL a réussi
                if ($result) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['id_demande']}'>
                                    <td>{$row['id_demande']}</td>
                                    <td>{$row['nom_article']}</td>
                                    <td>{$row['quantite']}</td>
                                    <td>{$row['prenom_agent']} {$row['nom_agent']}</td>
                                    <td>{$row['date_demande']}</td>
                                    <td>
                                        <form action='../backend/confirmer_livraison.php' method='post' onsubmit=\"removeRow('row-{$row['id_demande']}'); return true;\" style='display:inline-block;'>
                                            <input type='hidden' name='id_demande' value='{$row['id_demande']}'>
                                            <button type='submit' class='btn btn-primary'><i class='fas fa-hand-holding'></i> Livrer</button>
                                        </form>
                                        <form action='../backend/impression_bordereau.php' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='id_demande' value='{$row['id_demande']}'>
                                            <button type='submit' class='btn btn-secondary'><i class='fas fa-print'></i> Imprimer Bordereau</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Aucune demande prête à livrer.</td></tr>";
                    }
                } else {
                    echo "Erreur dans la requête SQL : " . $conn->error;
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
