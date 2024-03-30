<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(array("message" => "Erreur : Utilisateur non connecté"));
    exit();
}
$userId = $_SESSION['id'];

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
$user = 'lv200932';
$password_db = 'lv200932';

try {
    $connexion = new PDO($dsn, $user, $password_db);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        // Mapping des actions
        $columnMapping = array(
            'edit_nom_utilisateur' => 'nom_utilisateur',
            'edit_prenom' => 'prenom',
            'edit_nom' => 'nom',
            'edit_bio' => 'biographie',
            'edit_mail' => 'email',
            'edit_tel' => 'tel'
        );

        if (array_key_exists($action, $columnMapping)) {
            $columnName = $columnMapping[$action];

            $nouvelleValeur = isset($_POST["nouvelle_valeur"]) ? htmlspecialchars($_POST["nouvelle_valeur"]) : '';

            if (!empty($nouvelleValeur)) {
                $sql = "UPDATE utilisateur SET $columnName = :nouvelleValeur WHERE id = :userId";
                $stmt = $connexion->prepare($sql);
                $stmt->bindParam(':nouvelleValeur', $nouvelleValeur);
                $stmt->bindParam(':userId', $userId);

                try {
                    $stmt->execute();
                    $_SESSION[$columnName] = $nouvelleValeur;
                    echo json_encode(array("message" => "Modification réussie"));
                    exit();
                } catch (PDOException $e) {
                    echo json_encode(array("message" => "Erreur lors de la modification : " . $e->getMessage()));
                    exit();
                }
            } else {
                echo json_encode(array("message" => "Erreur : Aucune nouvelle valeur fournie."));
                exit();
            }
        } else {
            echo json_encode(array("message" => "Erreur : Action non reconnue."));
            exit();
        }
    }
} catch (PDOException $e) {
    echo json_encode(array("message" => "Erreur de connexion : " . $e->getMessage()));
    exit();
}

$connexion = null;
?>
