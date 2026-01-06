<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord du Chef</title>
    <link rel="stylesheet" href="styles/chef_dashboard.css">
    <script>
        function validateQuantity(input, maxQuantity) {
            if (parseInt(input.value) > maxQuantity) {
                input.value = maxQuantity;
                alert("La quantité saisie ne peut pas dépasser la quantité demandée.");
            }
        }
    </script>
</head>
<body>
    <header>
            <div class="logo">
                <img src="asset/logo.png" alt="Logo de l'entreprise">
            </div>
            <nav>
                <ul>
                    <li><a href="chef_dashboard.php">Demandes en attente</a></li>
                    <li><a href="historique_agents.php">Historique des agents</a></li>
                    <li><a href="../backend/logout.php">Déconnexion</a></li>
                </ul>
            </nav>

    </header>

    <h1>Tableau de bord du Chef</h1>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID Demande</th>
                    <th>Nom de l'article</th>
                    <th>Quantité demandée</th>
                    <th>Agent</th>
                    <th>Date de demande</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connexion à la base de données
                require_once __DIR__ . '/../config/db_connect.php';

                // Récupérer toutes les demandes en attente
                $sql = "SELECT d.id_demande, d.nom_article, d.quantite, a.nom AS nom_agent, d.date_demande 
                        FROM demandes d
                        JOIN agents a ON d.id_agent = a.id_agent
                        WHERE d.etat = 'En attente'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id_demande']}</td>
                                <td>{$row['nom_article']}</td>
                                <td>{$row['quantite']}</td>
                                <td>{$row['nom_agent']}</td>
                                <td>{$row['date_demande']}</td>
                                <td>
                                    <form action='../backend/traiter_demande.php' method='post' class='action-form'>
                                        <input type='hidden' name='id_demande' value='{$row['id_demande']}'>
                                        <input type='number' name='quantite_validee' placeholder='Quantité' value='{$row['quantite']}' min='1' max='{$row['quantite']}' oninput='validateQuantity(this, {$row['quantite']})'>
                                        <button type='submit' name='action' value='valider' class='btn btn-success'>Valider</button>
                                        <button type='submit' name='action' value='refuser' class='btn btn-danger'>Refuser</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucune demande en attente.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
