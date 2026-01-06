<?php
require_once __DIR__ . '/../config/db_connect.php';
require('../fpdf/fpdf.php'); // Assurez-vous que le chemin est correct

// Créer un PDF avec FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Titre
$pdf->Cell(0, 10, 'Rapport de Stock', 0, 1, 'C');

// Espacement
$pdf->Ln(10);

// En-têtes de colonne
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Nom de l\'article', 1);
$pdf->Cell(40, 10, '             Stock', 1);
$pdf->Cell(50, 10, 'Quantite reservee', 1);
$pdf->Cell(50, 10, 'Stock disponible', 1);
$pdf->Ln();

// Récupérer les données des articles
$sql = "SELECT nom_article, stock, quantite_reservee, (stock - quantite_reservee) AS stock_disponible FROM article";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pdf->SetFont('Arial', '', 12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(50, 10, $row['nom_article'], 1);
        $pdf->Cell(40, 10, $row['stock'], 1);
        $pdf->Cell(50, 10, $row['quantite_reservee'], 1);
        $pdf->Cell(50, 10, $row['stock_disponible'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Aucun article en stock.', 1, 1, 'C');
}
// Espacement
$pdf->Ln(40);
    
// Signatures
$pdf->Cell(100, 10, 'Signature :............................... ', 0, 0, 'L');

$conn->close();

// Sortie du PDF
$pdf->Output();
?>
