<?php
session_start(); // Démarre la session

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

    // Vérifie si le formulaire de commentaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_publication = $_POST['id_publication'];
        $id_utilisateur = $_SESSION['id'];
        $contenu_commentaire = $_POST['commentaire'];

        // ajoutes le commentaire dans la base de données
        $sql_insert_commentaire = "INSERT INTO commentaires (id_publication, id_personne_commente, contenu) VALUES (:id_publication, :id_personne_commente, :contenu_commentaire)";

        $stmt_insert_commentaire = $connexion->prepare($sql_insert_commentaire);
        $stmt_insert_commentaire->bindParam(':id_publication', $id_publication);
        $stmt_insert_commentaire->bindParam(':id_personne_commente', $id_utilisateur);
        $stmt_insert_commentaire->bindParam(':contenu_commentaire', $contenu_commentaire);
        $stmt_insert_commentaire->execute();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Commentaire ajouté avec succès']);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Méthode de requête non autorisée']);
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
