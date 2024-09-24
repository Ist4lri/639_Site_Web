<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Récupérer les données du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $missions = $_POST['missions'];
    $id_mappeur = $_POST['mappeur'];
    $id_zeus1 = $_POST['zeus1'] ?? null;
    $id_zeus2 = $_POST['zeus2'] ?? null; // Peut être vide
    $id_zeus3 = $_POST['zeus3'] ?? null; // Peut être vide

    // Afficher les données récupérées pour débogage
    var_dump($date, $nom, $missions, $id_mappeur, $id_zeus1, $id_zeus2, $id_zeus3);

    try {
        // Requête d'insertion dans la table campagne
        $stmt = $pdo->prepare("INSERT INTO campagne (date, nom, missions, id_mappeur, id_zeus, id_zeus2, id_zeus3) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$date, $nom, $missions, $id_mappeur, $id_zeus1, $id_zeus2, $id_zeus3]);

        // Redirection ou message de confirmation
        header('Location: campagne.php');
        exit();
    } catch (PDOException $e) {
        // Afficher l'erreur SQL pour débogage
        echo "Erreur SQL : " . $e->getMessage();
    }
}
