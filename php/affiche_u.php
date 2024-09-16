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

require('../vendor/setasign/fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // Ajout de l'image de fond
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
    }
}

if ($utilisateur) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(0, 10, utf8_decode('Informations Personnelles'), 0, 1, 'C');
    $pdf->Ln(15);

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Utilisateur: ') . utf8_decode($utilisateur['nom']), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Grade: ') . utf8_decode($utilisateur['grade']), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Spécialité: ') . utf8_decode($utilisateur['spe']), 0, 1);
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Âge: ') . ($utilisateur['age'] ?: 'Non spécifié'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Taille: ') . ($utilisateur['taille'] ?: 'Non spécifié') . ' cm', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Poids: ') . ($utilisateur['poids'] ?: 'Non spécifié') . ' kg', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Problèmes médicaux: '), 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, utf8_decode($utilisateur['problemes_medicaux'] ?: 'Aucun problème médical spécifié'));
    $pdf->Ln(10);

    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, utf8_decode('Histoire: '), 0, 1);
    $pdf->SetX(25);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(150, 10, utf8_decode($utilisateur['histoire'] ?: 'Histoire non disponible'));
    $pdf->Ln(10);
}

$pdf->Output('I', 'Info-perso.pdf');
?>
