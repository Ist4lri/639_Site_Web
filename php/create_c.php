<?php
include 'db.php'; // Ensure this includes your database connection code

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $missions = $_POST['missions'];
    $id_mappeur = $_POST['mappeur'] ? $_POST['mappeur'] : null;
    $id_zeus = $_POST['zeus'] ? $_POST['zeus'] : null;

    // Insert data into the database
    $sql = "INSERT INTO campagne (date, nom, missions, id_mappeur, id_zeus) 
            VALUES (:date, :nom, :missions, :id_mappeur, :id_zeus)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':missions', $missions);
    $stmt->bindParam(':id_mappeur', $id_mappeur, PDO::PARAM_INT);
    $stmt->bindParam(':id_zeus', $id_zeus, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "La nouvelle campagne a été créée avec succès.";
        header("Location: campagne.php"); // Redirect back to the table view
        exit;
    } else {
        echo "Une erreur est survenue lors de la création de la campagne.";
    }
} else {
    header("Location: campagne.php"); // Redirect back if the request method is not POST
    exit;
}
