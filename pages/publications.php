<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

date_default_timezone_set('Europe/Paris');

$nom_utilisateur = $_SESSION['nom_utilisateur'];
$is_admin = $_SESSION['is_admin'];
$id_utilisateur_connecte = $_SESSION['id'];

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

$id_utilisateur_ext = $_SESSION['id'];

// Traitement de la recherche par date
$resultats_date = array();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_by_date'])) {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Requête SQL avec filtrage par date pour toutes les publications
    $sql_recherche_date = "SELECT * FROM publications WHERE date_post BETWEEN :date_debut AND :date_fin ORDER BY date_post DESC";
    $stmt_recherche_date = $connexion->prepare($sql_recherche_date);
    $stmt_recherche_date->bindParam(':date_debut', $date_debut);
    $stmt_recherche_date->bindParam(':date_fin', $date_fin);
    $stmt_recherche_date->execute();

    // Récupérer les résultats
    $resultats_date = $stmt_recherche_date->fetchAll(PDO::FETCH_ASSOC);
}
?>

<html>

<head>
    <meta charset="utf-8">
    <title>Harmony Network</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />
    <script src="./scripts.js"></script>
    <link rel="icon" href="../images/logo_harmony_couleur.png" type="image/png">
</head>

<body>
    <span class="flex-page">
        <!-- MENU DE GAUCHE -->
        <div class="menu_gauche" <?php if ($is_admin == 1): ?>style="background: url('../images/cadenas_admin.png');background-position: center; background-repeat: no-repeat; background-size: cover;"<?php endif; ?>>
            <img src="../images/logo_harmony.png" alt="logo" class="logo" onclick="accueil_click()">
            <p class="nom_site">Harmony</p>

            <div class="image_menu"><img src="../images/home.png" alt="account" class="images_menu_gauche" onclick="accueil_click()"><div class="description_actions"><p>Accueil</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/account.png" alt="account" class="images_menu_gauche" onclick="account_click()"><div class="description_actions"><p>Mon profil</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/rechercher.png" alt="rechercher" class="images_menu_gauche" onclick="search_click()"><div class="description_actions"><p>Rechercher</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/message.png" alt="messages" class="images_menu_gauche" onclick="messages_click()">
                <div class="description_actions"><p>Messages</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/planning.png" alt="planning" class="images_menu_gauche" onclick="publications_click()"><div class="description_actions"><p>Publications</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/settings.png" alt="settings" class="images_menu_gauche" onclick="settings_click()"><div class="description_actions"><p>Paramètres</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/exit.png" alt="logout" class="images_menu_gauche" onclick="logout()"><div class="description_actions"><p>Déconnexion</p></div></div>
        </div>

        <!-- BLOC CENTRAL -->
        <div class="bloc_principal">
            <h1>Rechercher des publications par date</h1>
            <br> <br>
            <!-- Formulaire de recherche par date -->
            <form action="" method="post">
                <label for="date_debut">Date de début :</label>
                <input type="date" name="date_debut" id="date_debut" required>

                <label for="date_fin">| Date de fin :</label>
                <input type="date" name="date_fin" id="date_fin" required>

                <!-- Bouton de validation -->
                <br><br>
                <button type="submit" name="search_by_date" class="button_base">Rechercher</button>
            </form>

            <!-- Affichage des résultats de la recherche par date -->
            <?php if (!empty($resultats_date)): ?>
                <br>
                <div class="zone_titre">
                    <p><span>Publications dans la plage de dates sélectionnée</span></p>
                </div>
                <br>
                <?php foreach ($resultats_date as $resultat): ?>
                    <?php
                    $is_administr = ($is_admin == 1);
                    date_default_timezone_set('Europe/Paris');
        
                    // Requête pour récupérer le nom de l'utilisateur qui a posté la publication
                    $sql_info_posteur = "SELECT nom_utilisateur FROM utilisateur WHERE id = :id_posteur";
                    $stmt_info_posteur = $connexion->prepare($sql_info_posteur);
                    $stmt_info_posteur->bindParam(':id_posteur', $resultat['user_id']);
                    $stmt_info_posteur->execute();
                    $info_posteur = $stmt_info_posteur->fetch(PDO::FETCH_ASSOC);

                    // Requête pour compter les likes et dislikes
                    $id_publication = $resultat['id'];
                    $sql_likes = "SELECT SUM(compteur_like) as totalLikes, SUM(compteur_dislike) as totalDislikes FROM likes WHERE id_publication = :id_publication";
                    $stmt_likes = $connexion->prepare($sql_likes);
                    $stmt_likes->bindParam(':id_publication', $id_publication);
                    $stmt_likes->execute();
                    $likes_info = $stmt_likes->fetch(PDO::FETCH_ASSOC);
                  
                    $totalLikes = $likes_info['totalLikes'] ?? 0;
                    $totalDislikes = $likes_info['totalDislikes'] ?? 0;
                    ?>
                      <div class="harmony_post_conf">
                          <span class="espace_droit_post"></span>
                          <?php echo "Par " ?>
                          <?php if ($resultat['user_id'] == $id_utilisateur_connecte): ?>
                              <a href="./account.php" style="color:white;">@<?php echo htmlspecialchars($info_posteur['nom_utilisateur']); ?></a>
                          <?php else: ?>
                              <a href="./utilisateur_ext.php?id=<?php echo $resultat['user_id']; ?>" style="color:white;">
                                  <?php echo "@" . htmlspecialchars($info_posteur['nom_utilisateur']); ?>
                              </a>
                          <?php endif; ?>

                        <?php echo "|" ?>
                        <?php echo date('d/m/Y à H\hi', strtotime($resultat['date_post'])); ?>
                        <?php echo "| en" ?>
                        <?php echo $resultat['public'] == 0 ? "Privé 🔒" : "Publique 🌐"; ?>
                        <br><br>
                        <div class="afficheur_poste_harmony">
                            <p><?php echo $resultat['contenu']; ?></p>
                        </div>
                        
                        
                        <br>
                      <!-- Affiche les commentaires -->
                      <?php
                      $id_publication = $resultat['id'];
                      $sql_commentaires = "SELECT commentaires.*, utilisateur.nom_utilisateur AS nom_utilisateur_commentaire FROM commentaires INNER JOIN utilisateur ON commentaires.id_personne_commente = utilisateur.id WHERE id_publication = :id_publication ORDER BY date_commentaire DESC";
                      $stmt_commentaires = $connexion->prepare($sql_commentaires);
                      $stmt_commentaires->bindParam(':id_publication', $id_publication);
                      $stmt_commentaires->execute();
                      $commentaires = $stmt_commentaires->fetchAll(PDO::FETCH_ASSOC);
                      echo "<div class='affichage_comm'>";
                      foreach ($commentaires as $commentaire) {
                          $date_commentaire = date('d/m/Y à H\hi', strtotime($commentaire['date_commentaire']));
                          $is_comment_owner = ($commentaire['id_personne_commente'] == $id_utilisateur_connecte);

                          echo "<div class='commentaire'>
                                  <span class='commentaire-utilisateur'>
                                      Par ";

                          // Condition pour le lien en fonction de l'auteur du commentaire
                          if ($is_comment_owner) {
                              echo "<a href='./account.php' style='color:white;'>@{$commentaire['nom_utilisateur_commentaire']}</a>";
                          } else {
                              echo "<a href='./utilisateur_ext.php?id={$commentaire['id_personne_commente']}' style='color:white;'>@{$commentaire['nom_utilisateur_commentaire']}</a>";
                          }

                          echo "</span>
                                  <span class='commentaire-date'> | {$date_commentaire}</span>
                                  <div class='flexcomm'>
                                      <p>{$commentaire['contenu']}</p>";

                          // Suppression uniquement si l'utilisateur connecté est l'auteur du commentaire
                          if ($is_comment_owner || $is_administr) {
                              echo "<button onclick='deleteComment({$commentaire['id']})'>❌</button>";
                          }

                          echo "</div></div>";
                      }
                      echo "</div>";
                      ?>

                      <span class="boutons_likes">
                          <img src="../images/like_bouton.png" alt="Like" class="btn_like_dis" onclick="toggleLike(<?php echo $resultat['id']; ?>, 'like')">
                          <br>
                          <span id="likeCount_<?php echo $id_publication; ?>"><?php echo $totalLikes; ?></span>
                          <img src="../images/dislike_bouton.png" alt="Dislike" class="btn_like_dis" onclick="toggleLike(<?php echo $resultat['id']; ?>, 'dislike')">
                          <br>
                          <span id="dislikeCount_<?php echo $id_publication; ?>"><?php echo $totalDislikes; ?></span>
                      </span>
                    </div>
                    <br>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </span>
</body>
</html>
