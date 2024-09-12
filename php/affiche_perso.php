<?php
session_start();
require 'db.php';
require('../vendor/setasign/fpdf/fpdf.php');  // Include FPDF

// Check if an ID is passed
if (!isset($_GET['id'])) {
    die('No character ID provided.');
}

$id = $_GET['id'];

// Fetch the character from the database
$sql = "SELECT nom, faction, histoire FROM personnages WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$perso = $stmt->fetch(PDO::FETCH_ASSOC);

// If no character is found
if (!$perso) {
    die('Character not found.');
}

// Create a new FPDF instance
$pdf = new FPDF();
$pdf->AddPage();  // Add a new page
$pdf->SetFont('Arial', 'B', 16);  // Set the font

// Set Title
$pdf->Cell(0, 10, 'Validation du Personnage', 0, 1, 'C');

// Line break
$pdf->Ln(10);

// Set the character name
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nom: ', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, $perso['nom'], 0, 1);

// Set the faction
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Faction: ', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, $perso['faction'], 0, 1);

// Line break
$pdf->Ln(10);

// Set the history
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Histoire: ', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, $perso['histoire']);


$raison = !empty($perso['raison']) ? $perso['raison'] : '';  // Vérification de la raison
$pdf->MultiCell(0, 10, $raison);


// Output the PDF to the browser
$pdf->Output("I", "Validation_Personnage_{$perso['nom']}.pdf");

?>
