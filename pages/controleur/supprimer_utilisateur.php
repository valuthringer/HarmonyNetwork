<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_POST['id_utilisateur_ext'])) {
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

$id_utilisateur_ext = $_POST['id_utilisateur_ext'];

try {
    // Désactiver les contraintes de clés étrangères
    $connexion->exec('SET foreign_key_checks = 0');
    
    // Supprimer les likes liés à l'utilisateur
    $sql_suppr_likes = "DELETE FROM likes WHERE id_personne_like = :id_utilisateur_ext";
    $stmt_suppr_likes = $connexion->prepare($sql_suppr_likes);
    $stmt_suppr_likes->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_likes->execute();

    // Supprimer les commentaires liés à l'utilisateur
    $sql_suppr_commentaires = "DELETE FROM commentaires WHERE id_personne_commente = :id_utilisateur_ext";
    $stmt_suppr_commentaires = $connexion->prepare($sql_suppr_commentaires);
    $stmt_suppr_commentaires->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_commentaires->execute();

    // Supprimer les abonnements liés à l'utilisateur
    $sql_suppr_abonnements = "DELETE FROM abonnements WHERE id_utilisateur = :id_utilisateur_ext";
    $stmt_suppr_abonnements = $connexion->prepare($sql_suppr_abonnements);
    $stmt_suppr_abonnements->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_abonnements->execute();

    // Supprimer les abonnés de l'utilisateur (de la table abonnements)
    $sql_suppr_abonnes = "DELETE FROM abonnements WHERE id_abonnement = :id_utilisateur_ext";
    $stmt_suppr_abonnes = $connexion->prepare($sql_suppr_abonnes);
    $stmt_suppr_abonnes->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_abonnes->execute();

    // Supprimer les messages de l'utilisateur
    $sql_suppr_messages = "DELETE FROM publications WHERE user_id = :id_utilisateur_ext";
    $stmt_suppr_messages = $connexion->prepare($sql_suppr_messages);
    $stmt_suppr_messages->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_messages->execute();

    // Supprimer l'utilisateur
    $sql_suppr_utilisateur = "DELETE FROM utilisateur WHERE id = :id_utilisateur_ext";
    $stmt_suppr_utilisateur = $connexion->prepare($sql_suppr_utilisateur);
    $stmt_suppr_utilisateur->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
    $stmt_suppr_utilisateur->execute();

    // Réactiver les contraintes de clés étrangères
    $connexion->exec('SET foreign_key_checks = 1');

    header("Location: ../recherche.php");
    exit();
} catch (PDOException $e) {
    // Réactiver les contraintes de clés étrangères en cas d'erreur
    $connexion->exec('SET foreign_key_checks = 1');

    echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    exit();
}
?>
