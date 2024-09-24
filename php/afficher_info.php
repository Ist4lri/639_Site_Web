<?php
session_start();
include 'db.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Suppression de la référence à date_service
// Récupérer les informations médicales de l'utilisateur
$stmt = $pdo->prepare("SELECT u.nom, im.age, im.taille, im.poids, im.problemes_medicaux, im.groupe_sanguin, im.monde_origine, 
                       im.antecedents_biologiques, im.antecedents_psychologiques, im.fumeurs, im.allergies, im.intolerances, 
                       im.date_modification, im.temps_service 
                       FROM utilisateurs u 
                       LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur 
                       WHERE u.id = ?");
$stmt->execute([$id_utilisateur]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

var_dump($userInfo); // Vérifier que les informations médicales sont récupérées

// Gestion de la colonne temps_service
$tempsService = !empty($userInfo['temps_service']) ? $userInfo['temps_service'] . ' années' : 'Non spécifié';

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
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Utilisateur: ' . $userInfo['nom'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Date de modification: ' . date('d/m/Y', strtotime($userInfo['date_modification'])), 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Âge: ' . $userInfo['age'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Groupe Sanguin: ' . $userInfo['groupe_sanguin'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Taille: ' . $userInfo['taille'] . ' cm', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Poids: ' . $userInfo['poids'] . ' kg', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Temps de service: ' . $tempsService, 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Monde d\'origine: ' . $userInfo['monde_origine'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Antécédents biologiques/physiques: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->MultiCell(150, 10, mb_convert_encoding($userInfo['antecedents_biologiques'], 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(5);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Antécédents psychologiques: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->MultiCell(150, 10, mb_convert_encoding($userInfo['antecedents_psychologiques'], 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(5);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Fumeurs: ' . ($userInfo['fumeurs'] ? 'Oui' : 'Non'), 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Allergies: ' . $userInfo['allergies'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Intolérances: ' . $userInfo['intolerances'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Ln(10);
$pdf->SetX(25);
$pdf->Cell(0, 10, mb_convert_encoding('Commentaire : ', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetX(25);
$pdf->MultiCell(150, 10, mb_convert_encoding($userInfo['problemes_medicaux'], 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(10);

// Générer le PDF
$pdf->Output('I', 'informations_medicales.pdf');
?>
