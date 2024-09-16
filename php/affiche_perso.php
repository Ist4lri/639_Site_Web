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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

require('../vendor/setasign/fpdf/fpdf.php');
class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
        if ($perso['validation'] === 'Accepter') {
    $pdf->Image('../src/assets/sceau.png', (($pdf->GetPageWidth() - 40) / 2) - 1, 240, 40);
        }
    }
}



if (!$perso) {
    die('Non Existant');
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
$pdf->MultiCell(150, 10, $perso['histoire']);

$pdf->SetX(25);

$raison = !empty($perso['raison']) ? $perso['raison'] : '';
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, $raison, 0);


if ($perso['validation'] === 'Accepter') {
    $pdf->Image('../src/assets/sceau.png', (($pdf->GetPageWidth() - 40) / 2) - 1, 240, 40);
}

$pdf->Output("I", "Validation_Personnage_{$perso['nom']}.pdf");
?>
