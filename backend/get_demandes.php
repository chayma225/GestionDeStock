<?php
require_once __DIR__ . '/../config/db_connect.php';

$id_agent = isset($_GET['id_agent']) ? intval($_GET['id_agent']) : 0;

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$sql = "SELECT id_demande, nom_article, quantite, date_demande, etat FROM demandes WHERE id_agent = $id_agent";
$result = $conn->query($sql);

if ($result === false) {
    echo "Erreur de requête SQL : " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID Demande</th>
                        <th>Nom de l'article</th>
                        <th>Quantité</th>
                        <th>Date de demande</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_demande']}</td>
                    <td>{$row['nom_article']}</td>
                    <td>{$row['quantite']}</td>
                    <td>{$row['date_demande']}</td>
                    <td>{$row['etat']}</td>
                  </tr>";
        }
        echo "</tbody>
            </table>";
    } else {
        echo "Aucune demande trouvée pour cet agent.";
    }
}

$conn->close();
?>
