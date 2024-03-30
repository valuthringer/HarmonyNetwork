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

// traitement des likes et dislikes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if (isset($data['postId']) && isset($data['action'])) {
        $postId = $data['postId'];
        $action = $data['action'];

        // vérifie si l'utilisateur a deja like / disliké la publi
        $userId = $_SESSION['id'];
        $checkSql = "SELECT * FROM likes WHERE id_personne_like = :userId AND id_publication = :postId";
        $checkStmt = $connexion->prepare($checkSql);
        $checkStmt->bindParam(':userId', $userId);
        $checkStmt->bindParam(':postId', $postId);
        $checkStmt->execute();
        $existingLike = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Mets à jour la base de données
        if ($existingLike) {
            $updateSql = "UPDATE likes SET compteur_like = :compteurLike, compteur_dislike = :compteurDislike WHERE id_personne_like = :userId AND id_publication = :postId";
        } else {
            $updateSql = "INSERT INTO likes (id_personne_like, id_publication, compteur_like, compteur_dislike) VALUES (:userId, :postId, :compteurLike, :compteurDislike)";
        }

        switch ($action) {
            case 'like':
                $compteurLike = 1;
                $compteurDislike = 0;
                break;
            case 'dislike':
                $compteurLike = 0;
                $compteurDislike = 1;
                break;
            default:
                http_response_code(400);
                exit();
        }

        $updateStmt = $connexion->prepare($updateSql);
        $updateStmt->bindParam(':userId', $userId);
        $updateStmt->bindParam(':postId', $postId);
        $updateStmt->bindParam(':compteurLike', $compteurLike);
        $updateStmt->bindParam(':compteurDislike', $compteurDislike);
        $updateStmt->execute();

        // Récupère les nouveaux compteurs de likes et dislikes
        $likesSql = "SELECT SUM(compteur_like) AS totalLikes, SUM(compteur_dislike) AS totalDislikes FROM likes WHERE id_publication = :postId";
        $likesStmt = $connexion->prepare($likesSql);
        $likesStmt->bindParam(':postId', $postId);
        $likesStmt->execute();
        $likesData = $likesStmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($likesData);
        exit();
    } else {
        http_response_code(400);
        echo "Données manquantes dans la requête.";
        exit();
    }
} else {
    http_response_code(400);
    echo "Requête invalide.";
    exit();
}
?>
