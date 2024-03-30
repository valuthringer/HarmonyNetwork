<?php
session_start();

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

try {
    $connexion = new PDO($dsn, $user, $password_db);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['id'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
        exit();
    }

    // Vérifie si le formulaire de suppression de commentaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $commentId = $_POST['commentId'];
        $id_utilisateur = $_SESSION['id'];

        // Vérifie si l'utilisateur est l'auteur du commentaire
        $sql_check_author = "SELECT id_personne_commente FROM commentaires WHERE id = :commentId";
        $stmt_check_author = $connexion->prepare($sql_check_author);
        $stmt_check_author->bindParam(':commentId', $commentId);
        $stmt_check_author->execute();
        $commentAuthorId = $stmt_check_author->fetchColumn();

        $sql_delete_commentaire = "DELETE FROM commentaires WHERE id = :commentId";
        $stmt_delete_commentaire = $connexion->prepare($sql_delete_commentaire);
        $stmt_delete_commentaire->bindParam(':commentId', $commentId);
        $stmt_delete_commentaire->execute();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Le commentaire a été supprimé.']);
        exit();
        
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Requête non autorisée']);
        exit();
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Erreur PDO : ' . $e->getMessage()]);
    exit();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    exit();
}
?>