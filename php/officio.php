<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, nom, grade FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

$characterStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id AND faction = 'Officio Prefectus' AND validation = 'Accepter'");
$characterStmt->execute(['id' => $currentUser['id']]);
$character = $characterStmt->fetch();

if ($character) {
    include 'officio_prefectus_main.php';
} else {
    include 'officio_prefectus_plainte.php';
}
?>
