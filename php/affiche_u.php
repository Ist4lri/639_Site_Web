<?php

include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Préparer la requête pour obtenir les données de l'utilisateur
$stmt = $pdo->prepare("
    SELECT u.nom, u.grade, u.histoire, s.nom AS spe, 
           im.id AS info_id, im.age, im.taille, im.poids, im.problemes_medicaux 
    FROM utilisateurs u 
    LEFT JOIN spe s ON u.spe_id = s.id 
    LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur 
    WHERE u.id = :id
");
$stmt->execute(['id' => $_GET['id']]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: text/html; charset=utf-8'); // Assurez-vous que l'encodage est en UTF-8

require('../vendor/setasign/fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
    }
}

if ($utilisateur) {
    $pdf = new PDF();
    $pdf->AddPage();

    // Ajouter la police UTF-8 compatible
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.php');
    $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.php');
    
    $pdf->SetFont('DejaVu','',12);

    // Utiliser mb_convert_encoding pour convertir les chaînes en ISO-8859-1 avant de les passer à FPDF
    $pdf->Cell(0, 10, mb_convert_encoding('Informations Médicales', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(15);

    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Utilisateur: ' . $utilisateur['nom'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Grade: ' . $utilisateur['grade'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Spécialité: ' . $utilisateur['spe'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Ln(10);

    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Âge: ' . ($utilisateur['age'] ?: 'Non spécifié'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Taille: ' . ($utilisateur['taille'] ?: 'Non spécifié') . ' cm', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Poids: ' . ($utilisateur['poids'] ?: 'Non spécifié') . ' kg', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Problèmes médicaux: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, mb_convert_encoding($utilisateur['problemes_medicaux'] ?: 'Aucun problème médical spécifié', 'ISO-8859-1', 'UTF-8'));
    $pdf->Ln(10);

    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', 'B', 12);
    $pdf->Cell(40, 10, mb_convert_encoding('Histoire: ', 'ISO-8859-1', 'UTF-8'), 0, 1);

    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->MultiCell(150, 10, mb_convert_encoding($utilisateur['histoire'] ?: 'Histoire non disponible', 'ISO-8859-1', 'UTF-8'));
    $pdf->Ln(10);

    // Générer le PDF
    $pdf->Output('I', 'Info-perso.pdf');
}
