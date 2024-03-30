<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données du formulaire
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $birthdate = $_POST['birthdate'];

    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

    try {
        $connexion = new PDO($dsn, $user, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO utilisateur (email, mot_de_passe, nom_utilisateur, prenom, nom, telephone_portable, date_naissance) 
                VALUES (:email, :password, :username, :firstName, :lastName, :phone, :birthdate)";

        $stmt = $connexion->prepare($sql);

        // bind des paramètres
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':birthdate', $birthdate);

        $stmt->execute();

        header("Location: ../login.php");
        exit();

    } catch (PDOException $e) {
        echo "Erreur d'inscription : " . $e->getMessage();
    }

    $connexion = null;
}
?>
