<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

try {
    $connexion = new PDO($dsn, $user, $password_db);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit();
}

$id_utilisateur_connecte = $_SESSION['id'];

try {
    $connexion->beginTransaction();

    // Désactiver les contraintes de clés étrangères
    $connexion->exec('SET foreign_key_checks = 0');

    // Supprimer les likes liés à l'utilisateur
    $stmt_suppr_likes = $connexion->prepare("DELETE FROM likes WHERE id_personne_like = :id_utilisateur_connecte");
    $stmt_suppr_likes->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_likes->execute();

    // Supprimer les commentaires liés à l'utilisateur
    $stmt_suppr_commentaires = $connexion->prepare("DELETE FROM commentaires WHERE id_personne_commente = :id_utilisateur_connecte");
    $stmt_suppr_commentaires->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_commentaires->execute();

    // Supprimer les abonnements liés à l'utilisateur
    $stmt_suppr_abonnements = $connexion->prepare("DELETE FROM abonnements WHERE id_utilisateur = :id_utilisateur_connecte");
    $stmt_suppr_abonnements->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_abonnements->execute();

    // Supprimer les abonnés de l'utilisateur (de la table abonnements)
    $stmt_suppr_abonnes = $connexion->prepare("DELETE FROM abonnements WHERE id_abonnement = :id_utilisateur_connecte");
    $stmt_suppr_abonnes->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_abonnes->execute();

    // Supprimer les messages de l'utilisateur
    $stmt_suppr_messages = $connexion->prepare("DELETE FROM publications WHERE user_id = :id_utilisateur_connecte");
    $stmt_suppr_messages->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_messages->execute();

    // Supprimer l'utilisateur
    $stmt_suppr_utilisateur = $connexion->prepare("DELETE FROM utilisateur WHERE id = :id_utilisateur_connecte");
    $stmt_suppr_utilisateur->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
    $stmt_suppr_utilisateur->execute();

    // Réactiver les contraintes de clés étrangères
    $connexion->exec('SET foreign_key_checks = 1');

    $connexion->commit();

    
    session_destroy();

    header("Location: ../login.php");
    exit();
} catch (PDOException $e) {
    // Réactiver les contraintes de clés étrangères en cas d'erreur
    $connexion->exec('SET foreign_key_checks = 1');

    $connexion->rollBack();

    echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    exit();
}
?>
