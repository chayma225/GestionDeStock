<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Agents</title>
    <link rel="stylesheet" href="styles/historique_agents.css">
</head>
<body>
    <header>
        <h1>Historique des Agents</h1>
        <nav>
            <ul>
                <li><a href="chef_dashboard.php">Demandes en attente</a></li>
                <li><a href="historique_agents.php">Historique des agents</a></li>
                <li><a href="../backend/logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>

        <!-- Barre de recherche et filtres -->
        <div class="search-filter-container">
            <div class="search-filter">
                <input type="text" id="search-nom" placeholder="Rechercher par nom...">
                <input type="text" id="search-prenom" placeholder="Rechercher par prénom...">
                <input type="text" id="search-departement" placeholder="Rechercher par département...">
                <select id="search-role">
                    <option value="all">Tous les rôles</option>
                    <option value="agent">Agent</option>
                    <option value="chef">Chef</option>
                    <option value="magasinier">Magasinier</option>
                </select>
                <button onclick="applyFilters()">Filtrer</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Agent</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Rôle</th>
                    <th>Département</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="agents-table-body">
                <?php
                // Connexion à la base de données
                require_once __DIR__ . '/../config/db_connect.php';

                // Vérifier la connexion à la base de données
                if ($conn->connect_error) {
                    die("Erreur de connexion : " . $conn->connect_error);
                }

                // Récupérer l'historique des agents
                $sql = "SELECT id_agent, nom, prenom, role, departement FROM agents";
                $result = $conn->query($sql);

                // Vérifier si la requête a réussi
                if ($result === false) {
                    echo "Erreur de requête SQL : " . $conn->error;
                } else {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='agent-row'>
                                    <td>{$row['id_agent']}</td>
                                    <td>{$row['nom']}</td>
                                    <td>{$row['prenom']}</td>
                                    <td>{$row['role']}</td>
                                    <td>{$row['departement']}</td>
                                    <td><button onclick='consultDemande({$row['id_agent']})' class='btn-consult'>Consulter</button></td>
                                  </tr>
                                  <tr class='demandes-row' id='demandes-{$row['id_agent']}' style='display:none'>
                                    <td colspan='6'></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Aucun agent trouvé.</td></tr>";
                    }
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </main>

    <script>
        function applyFilters() {
            let searchNom = document.getElementById('search-nom').value.toLowerCase();
            let searchPrenom = document.getElementById('search-prenom').value.toLowerCase();
            let searchDepartement = document.getElementById('search-departement').value.toLowerCase();
            let searchRole = document.getElementById('search-role').value.toLowerCase();
            let rows = document.querySelectorAll('#agents-table-body .agent-row');

            rows.forEach(row => {
                let nom = row.cells[1].innerText.toLowerCase();
                let prenom = row.cells[2].innerText.toLowerCase();
                let departement = row.cells[4].innerText.toLowerCase();
                let role = row.cells[3].innerText.toLowerCase();

                let searchMatchNom = nom.includes(searchNom);
                let searchMatchPrenom = prenom.includes(searchPrenom);
                let searchMatchDepartement = departement.includes(searchDepartement);
                let filterMatchRole = searchRole === 'all' || role === searchRole.toLowerCase();

                if (searchMatchNom && searchMatchPrenom && searchMatchDepartement && filterMatchRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function consultDemande(id_agent) {
            let demandesRow = document.getElementById('demandes-' + id_agent);
            if (demandesRow.style.display === 'none') {
                fetch('../backend/get_demandes.php?id_agent=' + id_agent)
                .then(response => response.text())
                .then(data => {
                    demandesRow.cells[0].innerHTML = data;
                    demandesRow.style.display = '';
                })
                .catch(error => console.error('Erreur:', error));
            } else {
                demandesRow.style.display = 'none';
            }
        }
    </script>
</body>
</html>
