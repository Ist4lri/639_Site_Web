<?php
session_start();
include 'db.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté et a la bonne spécialité
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer toutes les informations médicales de la base de données
$sql = "SELECT im.*, u.nom AS nom_utilisateur FROM informations_medicales im 
        JOIN utilisateurs u ON im.id_utilisateur = u.id";
$stmt = $pdo->query($sql);
$informations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger FPDF (assurez-vous que le chemin est correct)
require('../vendor/setasign/fpdf/fpdf.php'); // Assurez-vous que ce chemin est valide

// Créer une instance de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Image('../src/assets/fond.jpg', 0, 0, 210, 297);

// Titre du PDF
$pdf->Cell(0, 10, 'Informations Medicales', 0, 1, 'C');
$pdf->Ln(10);

// Définir la police pour le contenu
$pdf->SetFont('Arial', '', 12);


    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Utilisateur: ' . $informations['nom_utilisateur'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Age: ' . $informations['age'], 0, 1);
    $pdf->SetX(25);    
    $pdf->Cell(0, 10, 'Taille: ' . $informations['taille'] . ' cm', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Poids: ' . $informations['poids'] . ' kg', 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, 'Problemes medicaux: ' . $informations['problemes_medicaux']);
    $pdf->Ln(10); 



$pdf->Output('I', 'informations_medicales.pdf');
?>
