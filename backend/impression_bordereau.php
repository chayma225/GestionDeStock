
    <?php
    require_once __DIR__ . '/../config/db_connect.php';
    require(__DIR__ . '/../fpdf/fpdf.php'); // Assurez-vous que le chemin est correct
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_demande = $_POST['id_demande'];
    
        // Vérifiez si l'ID de la demande est passé correctement
        if (empty($id_demande)) {
            die("Erreur : ID de la demande non spécifié.");
        }
    
        // Récupérer les informations de la demande et de l'agent
        $sql = "SELECT d.id_demande, d.nom_article, d.quantite, a.nom AS nom_agent, d.date_demande
                FROM demandes d
                JOIN agents a ON d.id_agent = a.id_agent
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
            $date_livraison = date("Y-m-d H:i:s"); // Date actuelle
    
            // Créer un PDF avec FPDF
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
    
            // Titre
            $pdf->Cell(0, 10, 'Bordereau de Livraison', 0, 1, 'C');
    
            // Espacement
            $pdf->Ln(10);
    
            // Informations de la demande alignées à gauche
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'ID Demande : ' . $row['id_demande'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Nom de l\'article : ' . $row['nom_article'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Quantite : ' . $row['quantite'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Agent : ' . $row['nom_agent'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Date de demande : ' . $row['date_demande'], 0, 1, 'L');
            $pdf->Cell(0, 10, 'Date de livraison : ' . $date_livraison, 0, 1, 'L');
    
            // Espacement
            $pdf->Ln(20);
    
            // Signatures
            $pdf->Cell(95, 10, 'Signature de l\'Agent :...............................  ', 0, 0, 'L');
            $pdf->Cell(95, 10, 'Signature du Magasinier :...............................  ', 0, 1, 'L');
    
            // Sortie du PDF
            $pdf->Output();
    
        } else {
            echo "Demande non trouvée.";
        }
    
        $stmt->close();
        $conn->close();
    }
    ?>
    

  