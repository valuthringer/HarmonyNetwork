<?php
session_start(); // Démarre la session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

    try {
        $connexion = new PDO($dsn, $user, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        $sql = "SELECT * FROM utilisateur WHERE nom_utilisateur = :username";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
            
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
      

        if ($stmt->rowCount() > 0 && password_verify($password, $userRow['mot_de_passe'])) {
            // Enregistre les informations de l'utilisateur dans la session
            $_SESSION['id'] = $userRow['id'];
            $_SESSION['nom_utilisateur'] = $userRow['nom_utilisateur'];
            $_SESSION['prenom'] = $userRow['prenom'];
            $_SESSION['nom'] = $userRow['nom'];
            $_SESSION['email'] = $userRow['email'];
            $_SESSION['telephone_portable'] = $userRow['telephone_portable'];
            $_SESSION['date_naissance'] = $userRow['date_naissance'];
            $_SESSION['biographie'] = $userRow['biographie'];
            $_SESSION['is_admin'] = $userRow['is_admin'];

            // Récupère les utilisateurs abonnés au compte courant
            $id_utilisateur_courant = $_SESSION['id'];
            $sql_abonnes = "SELECT utilisateur.nom_utilisateur
                            FROM utilisateur
                            JOIN abonnements ON utilisateur.id = abonnements.id_utilisateur
                            WHERE abonnements.id_abonnement = :id_utilisateur";
            $stmt_abonnes = $connexion->prepare($sql_abonnes);
            $stmt_abonnes->bindParam(':id_utilisateur', $id_utilisateur_courant);
            $stmt_abonnes->execute();
            $_SESSION['abonnes'] = $stmt_abonnes->fetchAll(PDO::FETCH_COLUMN);

            // Récupère les abonnements de l'utilisateur courant
            $sql_abonnements = "SELECT utilisateur.nom_utilisateur
                                FROM utilisateur
                                JOIN abonnements ON utilisateur.id = abonnements.id_abonnement
                                WHERE abonnements.id_utilisateur = :id_utilisateur";
            $stmt_abonnements = $connexion->prepare($sql_abonnements);
            $stmt_abonnements->bindParam(':id_utilisateur', $id_utilisateur_courant);
            $stmt_abonnements->execute();
            $_SESSION['abonnements'] = $stmt_abonnements->fetchAll(PDO::FETCH_COLUMN);

            // Compte le nombre d'abonnés et d'abonnements
            $_SESSION['nombre_abonnes'] = count($_SESSION['abonnes']);
            $_SESSION['nombre_abonnements'] = count($_SESSION['abonnements']);

            // Récupère les publications de l'utilisateur
            $sql_publications = "SELECT * FROM publications WHERE user_id = :user_id ORDER BY id DESC";
            $stmt_publications = $connexion->prepare($sql_publications);
            $stmt_publications->bindParam(':user_id', $_SESSION['id']);
            $stmt_publications->execute();
            $_SESSION['publications'] = $stmt_publications->fetchAll(PDO::FETCH_ASSOC);

            
          

            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Nom d'utilisateur ou mot de passe incorrect.";
            header("Location: ../login.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    } finally {
        $connexion = null;
    }
}
?>
