<?php
require_once __DIR__ . '/../config/db_connect.php';
require('../fpdf/fpdf.php'); // Assurez-vous que le chemin est correct

// Créer un PDF avec FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Titre
$pdf->Cell(0, 10, 'Rapport des Demandes', 0, 1, 'C');

// Espacement
$pdf->Ln(10);

// En-têtes de colonne
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Nom de l\'article', 1);
$pdf->Cell(30, 10, 'Quantit\e', 1);
$pdf->Cell(40, 10, 'Agent', 1);
$pdf->Cell(50, 10, 'Date de demande', 1);
$pdf->Ln();

// Récupérer les données des demandes
$sql = "SELECT d.id_demande, d.nom_article, d.quantite, a.prenom AS prenom_agent, a.nom AS nom_agent, d.date_demande
        FROM demandes d
        JOIN agents a ON d.id_agent = a.id_agent";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pdf->SetFont('Arial', '', 12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $row['id_demande'], 1);
        $pdf->Cell(50, 10, $row['nom_article'], 1);
        $pdf->Cell(30, 10, $row['quantite'], 1);
        $pdf->Cell(40, 10, $row['prenom_agent'] . ' ' . $row['nom_agent'], 1);
        $pdf->Cell(50, 10, $row['date_demande'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Aucune demande.', 1, 1, 'C');
}
 // Espacement
 $pdf->Ln(40);
    
 // Signatures
 $pdf->Cell(100, 10, 'Signature :...............................  ', 0, 0, 'L');

$conn->close();

// Sortie du PDF
$pdf->Output();
?>
