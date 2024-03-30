<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_publication'])) {
    $id_publication = $_POST['id_publication'];

    try {
        $dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
        $user = 'lv200932';
        $password_db = 'lv200932';

        $connexion = new PDO($dsn, $user, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_commentaires = "SELECT * FROM commentaires WHERE id_publication = :id_publication ORDER BY date_commentaire DESC LIMIT 3, 3";
        $stmt_commentaires = $connexion->prepare($sql_commentaires);
        $stmt_commentaires->bindParam(':id_publication', $id_publication);
        $stmt_commentaires->execute();
        $commentaires = $stmt_commentaires->fetchAll(PDO::FETCH_ASSOC);

        foreach ($commentaires as $commentaire) {
            echo "<div class='commentaire'>{$commentaire['contenu']}</div>";
        }
    } catch (PDOException $e) {
        echo "Erreur lors du chargement des commentaires : " . $e->getMessage();
    }
} else {
    // si erreur
    echo "Erreur de requête";
}
?>
