<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    die('No character ID provided.');
}

$id = $_GET['id'];

// Récupérer les informations du personnage
$sql = "SELECT nom, raison, faction, histoire, validation FROM personnages WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$perso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$perso) {
    die('Personnage non existant');
}

require('../vendor/setasign/fpdf/fpdf.php');
class PDF extends FPDF
{
    function Header()
    {
        // Image de fond
        $this->Image('../src/assets/fond.jpg', 0, 0, 210, 297);

        // Ajout du sceau si validation
        global $perso;
        if ($perso['validation'] === 'Accepter') {
            $this->Image('../src/assets/sceau.png', (($this->GetPageWidth() - 40) / 2) - 1, 240, 40);
        }
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
$pdf->SetMargins(15, 20, 15); // marges gauche, haut, droite
$pdf->AddPage();

// Ajouter les polices UTF-8 compatibles
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.php');
$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.php');

// Titre en rouge foncé
$pdf->SetTextColor(139, 0, 0); // Rouge foncé
$pdf->SetFont('DejaVu', 'B', 16);
$pdf->Cell(0, 10, mb_convert_encoding('Validation du Personnage', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(10);

// Remettre le texte en noir
$pdf->SetTextColor(0, 0, 0);

// Informations du personnage
$pdf->SetX(25);
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(40, 10, mb_convert_encoding('Nom: ' . $perso['nom'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Ln(8);

$pdf->SetX(25);
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(40, 10, mb_convert_encoding('Faction: ' . $perso['faction'], 'ISO-8859-1', 'UTF-8'), 0, 1);

// Afficher l'image pour la faction 'Adeptus Mechanicus'
if ($perso['faction'] === 'Adeptus Mechanicus') {
    $pdf->Image('../src/assets/mechanicus.png', 75, 20, 50);
}

$pdf->Ln(10);

// Histoire du personnage
$pdf->SetX(25);
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(40, 10, mb_convert_encoding('Histoire:', 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(25);
$pdf->SetFont('DejaVu', '', 12);
$pdf->MultiCell(150, 10, mb_convert_encoding($perso['histoire'], 'ISO-8859-1', 'UTF-8'));

$pdf->Ln(10);

// Raison si elle existe
$raison = !empty($perso['raison']) ? $perso['raison'] : '';
$pdf->SetX(25);
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(40, 10, mb_convert_encoding($raison, 'ISO-8859-1', 'UTF-8'), 0);

// Afficher le sceau si le personnage est validé
if ($perso['validation'] === 'Accepter') {
    $pdf->Image('../src/assets/sceau.png', (($pdf->GetPageWidth() - 40) / 2) - 1, 240, 40);
}

// Générer le PDF
$pdf->Output("I", "Validation_Personnage_{$perso['nom']}.pdf");

?>
