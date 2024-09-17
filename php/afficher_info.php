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

if (!isset($_SESSION['id_utilisateur'])) {
    die('Erreur: L\'ID utilisateur n\'est pas défini dans la session.');
}

if (!isset($_POST['id_utilisateur'])) {
    die("Erreur: aucun utilisateur spécifié.");
}

// Récupérer l'ID utilisateur
$id_utilisateur = $_POST['id_utilisateur'];

// Récupérer les informations médicales de l'utilisateur
$stmt = $pdo->prepare("SELECT u.nom, im.age, im.taille, im.poids, im.problemes_medicaux 
                       FROM utilisateurs u 
                       LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur 
                       WHERE u.id = ?");
$stmt->execute([$id_utilisateur]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);


if (empty($informations)) {
    die('Aucune information médicale trouvée pour cet utilisateur.');
}

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
    $pdf->Cell(0, 10, mb_convert_encoding('Utilisateur: ' . $userInfo['nom_utilisateur'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Âge: ' . $userInfo['age'], 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);    
    $pdf->Cell(0, 10, mb_convert_encoding('Taille: ' . $userInfo['taille'] . ' cm', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Poids: ' . $userInfo['poids'] . ' kg', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Problèmes médicaux: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->MultiCell(150, 10, mb_convert_encoding($userInfo['problemes_medicaux'], 'ISO-8859-1', 'UTF-8'));
    $pdf->Ln(10);

    // Vérifier si un saut de page est nécessaire
    if ($pdf->GetY() + 50 > 264) {
        $pdf->AddPage();
        $pdf->SetY(32);  // Ajuste la position du texte sur la nouvelle page
    }
}

// Générer le PDF
$pdf->Output('I', 'informations_medicales.pdf');
?>
