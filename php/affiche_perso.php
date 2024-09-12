<?php
session_start();
require 'db.php';
require('../vendor/setasign/fpdf/fpdf.php');


if (!isset($_GET['id'])) {
    die('No character ID provided.');
}

$id = $_GET['id'];


$sql = "SELECT nom, raison, faction, histoire, validation FROM personnages WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$perso = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$perso) {
    die('Character not found.');
}


$pdf = new FPDF();
$pdf->AddPage();


$pdf->Image('../src/assets/fond.jpg', 0, 0, 210, 297);  //A4 (210mm x 297mm)


$pdf->SetFont('Arial', 'B', 16);


$pdf->Cell(0, 10, 'Validation du Personnage', 0, 1, 'C');


$pdf->Ln(10);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nom: ', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, $perso['nom'], 0, 1);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Faction: ', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, $perso['faction'], 0, 1);


if ($perso['faction'] === 'Adeptus Mechanicus') {
    $pdf->Image('../src/assets/mechanicus.png', 150, 20, 50); 
}


$pdf->Ln(10);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Histoire: ', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, $perso['histoire']);

//
$raison = !empty($perso['raison']) ? $perso['raison'] : 'Aucune raison spécifiée';
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Raison: ', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, $raison);


if ($perso['validation'] === 'Accepter') {
    $pdf->Image('../src/assets/sceau.png', 150, 240, 40);  // Adjust size and position as necessary
}


$pdf->Output("I", "Validation_Personnage_{$perso['nom']}.pdf");

?>
