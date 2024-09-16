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

// S'assurer que tout est bien encodé en UTF-8
header('Content-Type: text/html; charset=utf-8'); 

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

    // Titre de la page
    $pdf->Cell(0, 10, utf8_decode('Informations Médicales'), 0, 1, 'C');
    $pdf->Ln(15);

    // Informations sur l'utilisateur
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Utilisateur: ') . utf8_decode($utilisateur['nom']), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Grade: ') . utf8_decode($utilisateur['grade']), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Spécialité: ') . utf8_decode($utilisateur['spe']), 0, 1);
    $pdf->Ln(10);

    // Informations médicales
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Âge: ') . ($utilisateur['age'] ?: utf8_decode('Non spécifié')), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Taille: ') . ($utilisateur['taille'] ?: utf8_decode('Non spécifié')) . ' cm', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Poids: ') . ($utilisateur['poids'] ?: utf8_decode('Non spécifié')) . ' kg', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, utf8_decode('Problèmes médicaux: '), 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, utf8_decode($utilisateur['problemes_medicaux'] ?: 'Aucun problème médical spécifié'));
    $pdf->Ln(10);

    // Histoire
    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', 'B', 12);
    $pdf->Cell(40, 10, utf8_decode('Histoire: '), 0, 1);

    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->MultiCell(150, 10, utf8_decode($utilisateur['histoire'] ?: 'Histoire non disponible'));
    $pdf->Ln(10);

    // Générer le PDF
    $pdf->Output('I', 'Info-perso.pdf');
}
