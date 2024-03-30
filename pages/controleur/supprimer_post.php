<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
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

// Vérifie si demande provient d'un formulaire POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_post_a_supprimer = $_POST['id_post'];

    try {
        // Supprime les likes associés a la publication
        $sql_supprimer_likes = "DELETE FROM likes WHERE id_publication = :id_post";
        $stmt_supprimer_likes = $connexion->prepare($sql_supprimer_likes);
        $stmt_supprimer_likes->bindParam(':id_post', $id_post_a_supprimer);
        $stmt_supprimer_likes->execute();

        // Supprime les commentaires associés à  la publication
        $sql_supprimer_commentaires = "DELETE FROM commentaires WHERE id_publication = :id_post";
        $stmt_supprimer_commentaires = $connexion->prepare($sql_supprimer_commentaires);
        $stmt_supprimer_commentaires->bindParam(':id_post', $id_post_a_supprimer);
        $stmt_supprimer_commentaires->execute();
  
        // Supprime la publication
        $sql_supprimer_post = "DELETE FROM publications WHERE id = :id_post";
        $stmt_supprimer_post = $connexion->prepare($sql_supprimer_post);
        $stmt_supprimer_post->bindParam(':id_post', $id_post_a_supprimer);
        $stmt_supprimer_post->execute();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression du post : " . $e->getMessage();
    }
}


header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>

