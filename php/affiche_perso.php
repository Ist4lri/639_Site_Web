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

$pdf->Image('../src/assets/fond.jpg', 0, 0, 210, 297);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Validation du Personnage', 0, 1, 'C');

$pdf->Ln(10);

$pdf->SetX(25);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nom: '. $perso['nom'], 0, 0);


$pdf->Ln(8); 

$pdf->SetX(25);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Faction: '. $perso['faction'], 0, 0);


if ($perso['faction'] === 'Adeptus Mechanicus') {
    $pdf->Image('../src/assets/mechanicus.png', 75, 20, 50);
}

$pdf->Ln(10);

$pdf->SetX(25);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Histoire:', 0, 1);

$pdf->SetX(25);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(40, 10, $perso['histoire']);

$pdf->SetX(25);

$raison = !empty($perso['raison']) ? $perso['raison'] : 'Aucune raison spécifiée';
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Raison:'. $raison, 0, 1);


if ($perso['validation'] === 'Accepter') {
    $pdf->Image('../src/assets/sceau.png', (($pdf->GetPageWidth() - 40) / 2) - 2, 240, 40);
}

$pdf->Output("I", "Validation_Personnage_{$perso['nom']}.pdf");
?>
