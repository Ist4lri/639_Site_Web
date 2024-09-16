<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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

    // Ajouter la police régulière
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.php');
    // Ajouter la police bold
    $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.php');
    
    $pdf->SetFont('DejaVu','',12);

    $pdf->Cell(0, 10, 'Informations Médicales', 0, 1, 'C');
    $pdf->Ln(15);

    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Utilisateur: ' . $utilisateur['nom'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Grade: ' . $utilisateur['grade'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Spécialité: ' . $utilisateur['spe'], 0, 1);
    $pdf->Ln(10);

    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Âge: ' . ($utilisateur['age'] ?: 'Non spécifié'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Taille: ' . ($utilisateur['taille'] ?: 'Non spécifié') . ' cm', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Poids: ' . ($utilisateur['poids'] ?: 'Non spécifié') . ' kg', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Problèmes médicaux: ', 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, $utilisateur['problemes_medicaux'] ?: 'Aucun problème médical spécifié');
    $pdf->Ln(10);


    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', 'B', 12);
    $pdf->Cell(40, 10, 'Histoire: ', 0, 1);

 
    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->MultiCell(150, 10, $utilisateur['histoire'] ?: 'Histoire non disponible');
    $pdf->Ln(10);


    // ob_clean(); 
    $pdf->Output('I', 'Info-perso.pdf');
}
?>
