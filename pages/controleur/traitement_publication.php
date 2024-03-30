<?php
session_start(); // Démarre la session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $message2 = $_POST['message2'];
    $is_private = isset($_POST['prive']) ? 0 : 1;

    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

    try {
        $connexion = new PDO($dsn, $user, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $user_id = $_SESSION['id'];

        // Créé le message avec la photo associée
        $message_final = $message . $message2;

        $sql_insert = "INSERT INTO publications (user_id, contenu, public) VALUES (:user_id, :contenu, :public)";
        $stmt_insert = $connexion->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $user_id);
        $stmt_insert->bindParam(':contenu', $message_final);
        $stmt_insert->bindParam(':public', $is_private);
        $stmt_insert->execute();

        header("Location: ../account.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    } finally {
        $connexion = null;
    }
}
?>
