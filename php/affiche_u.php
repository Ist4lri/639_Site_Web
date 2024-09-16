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
        // Utiliser une image de fond pour le header
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
        $this->SetY(32);
    }

    function Footer()
    {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-19);
        // Police Arial italique 8
        $this->SetFont('Arial', 'I', 8);
        // Texte centré de pied de page
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

if ($utilisateur) {
    $pdf = new PDF();
    
    // Définir les marges
    $pdf->SetMargins(15, 20, 15); // marges gauche, haut, droite
    $pdf->AddPage();

    // Ajouter les polices UTF-8 compatibles
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.php');
    $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.php');
    
  


   $pdf->SetTextColor(200, 0, 0); 
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(0, 10, mb_convert_encoding('Informations Personnelles', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(0);

$pdf->SetTextColor(0, 0, 0); 
      $pdf->SetFont('DejaVu','',12);
    // Informations sur l'utilisateur
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Utilisateur: ' . $utilisateur['nom'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Grade: ' . $utilisateur['grade'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Spécialité: ' . $utilisateur['spe'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Ln(5);

    // Informations médicales
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

    // Gestion des pages et contenu plus long
   if ($pdf->GetY() + 50 > 264) {
        $pdf->AddPage();
        $pdf->SetY(35);  // Ajuste la position du texte sur la nouvelle page
    }

    // Histoire
    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', 'B', 12);
    $pdf->Cell(40, 10, mb_convert_encoding('Histoire: ', 'ISO-8859-1', 'UTF-8'), 0, 1);

    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', '', 12);
    
    // Limiter la taille du bloc de texte pour qu'il s'adapte à la page actuelle et aux marges
    $histoire = $utilisateur['histoire'] ?: 'Histoire non disponible';
    $pdf->MultiCell(150, 10, mb_convert_encoding($histoire, 'ISO-8859-1', 'UTF-8'));
    
    // Ajouter un saut de page si nécessaire pour le contenu restant
    

    // Générer le PDF
    $pdf->Output('I', 'Info-perso.pdf');
}
