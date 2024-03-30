<?php
session_start(); // Démarre la session

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

try {
    $connexion = new PDO($dsn, $user, $password_db);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
    exit();
}

if (!isset($_SESSION['id']) || !isset($_POST['id_utilisateur'])) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit();
}

$id_utilisateur_connecte = $_SESSION['id'];
$id_utilisateur_cible = $_POST['id_utilisateur'];

// Vérifie si l'utilisateur connecté est déjà abonné à l'utilisateur cible
$sql_verif_abonnement = "SELECT * FROM abonnements WHERE id_utilisateur = :id_utilisateur_connecte AND id_abonnement = :id_utilisateur_cible";
$stmt_verif_abonnement = $connexion->prepare($sql_verif_abonnement);
$stmt_verif_abonnement->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
$stmt_verif_abonnement->bindParam(':id_utilisateur_cible', $id_utilisateur_cible);
$stmt_verif_abonnement->execute();
$est_abonne = $stmt_verif_abonnement->rowCount() > 0;

try {
    $connexion->beginTransaction();

    if ($est_abonne) {
        // désabonnement
        $sql_desabonnement = "DELETE FROM abonnements WHERE id_utilisateur = :id_utilisateur_connecte AND id_abonnement = :id_utilisateur_cible";
        $stmt_desabonnement = $connexion->prepare($sql_desabonnement);
        $stmt_desabonnement->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
        $stmt_desabonnement->bindParam(':id_utilisateur_cible', $id_utilisateur_cible);
        $stmt_desabonnement->execute();
        echo json_encode(['result' => 'desabonnement_reussi']);
    } else {
        // abonnement
        $sql_abonnement = "INSERT INTO abonnements (id_utilisateur, id_abonnement) VALUES (:id_utilisateur_connecte, :id_utilisateur_cible)";
        $stmt_abonnement = $connexion->prepare($sql_abonnement);
        $stmt_abonnement->bindParam(':id_utilisateur_connecte', $id_utilisateur_connecte);
        $stmt_abonnement->bindParam(':id_utilisateur_cible', $id_utilisateur_cible);
        $stmt_abonnement->execute();
        echo json_encode(['result' => 'abonnement_reussi']);
    }

    $connexion->commit();
} catch (PDOException $e) {
    $connexion->rollBack();
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
}
?>
