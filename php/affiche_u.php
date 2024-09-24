<?php
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'ID utilisateur a été passé via POST
if (!isset($_POST['id_utilisateur'])) {
    echo "Erreur : aucun utilisateur sélectionné.";
    exit();
}

$idUtilisateur = $_POST['id_utilisateur'];

// Requête pour récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("
    SELECT u.nom, u.grade, u.histoire, s.nom AS spe, 
           im.id AS info_id, im.age, im.taille, im.poids, im.problemes_medicaux, im.groupe_sanguin, im.monde_origine, 
           im.antecedents_biologiques, im.antecedents_psychologiques, im.fumeurs, im.allergies, im.intolerances, im.date_modification
    FROM utilisateurs u 
    LEFT JOIN spe s ON u.spe_id = s.id 
    LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur 
    WHERE u.id = :id
");
$stmt->execute(['id' => $idUtilisateur]);

// Stocker les résultats dans la variable $utilisateur
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur a été trouvé
if (!$utilisateur) {
    echo "Utilisateur non trouvé.";
    exit();
}

header('Content-Type: text/html; charset=utf-8'); 

require('../vendor/setasign/fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);
        $this->SetY(32);
    }

    function Footer()
    {
        $this->SetY(-19);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

if ($utilisateur) {
    $pdf = new PDF();
    
    $pdf->SetMargins(15, 20, 15);
    $pdf->AddPage();

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
    
    // Informations supplémentaires
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Groupe Sanguin: ' . ($utilisateur['groupe_sanguin'] ?: 'Non spécifié'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Monde d\'origine: ' . ($utilisateur['monde_origine'] ?: 'Non spécifié'), 'ISO-8859-1', 'UTF-8'), 0, 1);

    // Antécédents biologiques et psychologiques
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Antécédents biologiques: ' . ($utilisateur['antecedents_biologiques'] ?: 'Aucun'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Antécédents psychologiques: ' . ($utilisateur['antecedents_psychologiques'] ?: 'Aucun'), 'ISO-8859-1', 'UTF-8'), 0, 1);

    // Fumeurs, Allergies et Intolérances
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Fumeurs: ' . ($utilisateur['fumeurs'] ? 'Oui' : 'Non'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Allergies: ' . ($utilisateur['allergies'] ?: 'Aucune'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Intolérances: ' . ($utilisateur['intolerances'] ?: 'Aucune'), 'ISO-8859-1', 'UTF-8'), 0, 1);
    
    // Date de modification
    $pdf->SetX(25);
    $pdf->Cell(0, 10, mb_convert_encoding('Date de modification: ' . date('d/m/Y', strtotime($utilisateur['date_modification'])), 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Ln(10);

    // Histoire
    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', 'B', 12);
    $pdf->Cell(40, 10, mb_convert_encoding('Histoire: ', 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->SetX(25);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->MultiCell(150, 10, mb_convert_encoding($utilisateur['histoire'] ?: 'Histoire non disponible', 'ISO-8859-1', 'UTF-8'));

    $pdf->Output('I', 'Info-perso.pdf');
}
?>
