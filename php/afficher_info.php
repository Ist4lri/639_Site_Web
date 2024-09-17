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

header('Content-Type: text/html; charset=utf-8'); // Assurez-vous que l'encodage est en UTF-8

require('../vendor/setasign/fpdf/fpdf.php');

// Création de la classe PDF avec Header et Footer
class PDF extends FPDF
{
    function Header()
    {
        // Image de fond et images spécifiques
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
        $this->Image('../src/assets/medicae.png', 2, 146, 20); // largeur=20
        $this->Image('../src/assets/medicae.png', 189, 146, 20);
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

if ($informations) {

    // Créer une instance de la classe PDF
    $pdf = new PDF();
    
    // Définir les marges
    $pdf->SetMargins(15, 20, 15); // marges gauche, haut, droite
    $pdf->AddPage();

    // Ajouter les polices UTF-8 compatibles
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.php');
    $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.php');
    
    // Titre en rouge foncé
    $pdf->SetTextColor(139, 0, 0); // Rouge foncé
    $pdf->SetFont('DejaVu', 'B', 16);
    $pdf->Cell(0, 10, mb_convert_encoding('Informations Médicales', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(10);

    // Texte en noir
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('DejaVu','',12);

    // Parcourir les informations et les afficher dans le PDF
    foreach ($informations as $info) {
        $pdf->SetX(25);
        $pdf->Cell(0, 10, mb_convert_encoding('Utilisateur: ' . $info['nom_utilisateur'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $pdf->SetX(25);
        $pdf->Cell(0, 10, mb_convert_encoding('Âge: ' . $info['age'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $pdf->SetX(25);    
        $pdf->Cell(0, 10, mb_convert_encoding('Taille: ' . $info['taille'] . ' cm', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $pdf->SetX(25);
        $pdf->Cell(0, 10, mb_convert_encoding('Poids: ' . $info['poids'] . ' kg', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $pdf->SetX(25);
        $pdf->Cell(0, 10, mb_convert_encoding('Problèmes médicaux: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $pdf->SetX(25);
        $pdf->MultiCell(150, 10, mb_convert_encoding($info['problemes_medicaux'], 'ISO-8859-1', 'UTF-8'));
        $pdf->Ln(10);

        // Vérifier si un saut de page est nécessaire
        if ($pdf->GetY() + 50 > 264) {
            $pdf->AddPage();
            $pdf->SetY(32);  // Ajuste la position du texte sur la nouvelle page
        }
    }

    // Générer le PDF
    $pdf->Output('I', 'informations_medicales.pdf');
}
?>
