<?php
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($utilisateur) {
    require('../vendor/setasign/fpdf/fpdf.php');

    // Créer une instance de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Image('../src/assets/fond.jpg', 0, 0, 210, 297);


    $pdf->Cell(0, 10, 'Informations Medicales', 0, 1, 'C');
    $pdf->Ln(15);

    // Informations sur l'utilisateur
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Utilisateur: ' . $utilisateur['nom'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Grade: ' . $utilisateur['grade'], 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Specialite: ' . $utilisateur['spe'], 0, 1); // Spécialité sous le nom
    $pdf->Ln(10); 

    // Informations médicales
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Age: ' . $utilisateur['age'] ?: 'Nonspécifié' , 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Taille: ' . $utilisateur['taille'] ?: 'Nonspécifié' . ' cm', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Poids: ' . $utilisateur['poids'] ?: 'Nonspécifié'  . ' kg', 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, 'Problemes medicaux: ', 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, $utilisateur['problemes_medicaux'] ?: 'Aucun problème médical spécifié'); // Si null, affiche "Aucun problème médical spécifié"
    $pdf->Ln(10); 

    // Histoire
    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Histoire: ', 0, 1);
    $pdf->SetX(25);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, $utilisateur['histoire'] ?: 'Histoire non disponible'); // Si null, affiche "Histoire non disponible"
    $pdf->Ln(10); 
}


$pdf->Output('I', 'Info-perso.pdf');
?>
