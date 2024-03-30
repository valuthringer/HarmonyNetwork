<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}
date_default_timezone_set('Europe/Paris');
$nom_utilisateur = $_SESSION['nom_utilisateur'];
$is_admin = $_SESSION['is_admin'];

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

// Récupération des publications publiques depuis la base de données
try {
    $sql_publications_publiques = "SELECT * FROM publications WHERE public = 1 ORDER BY date_post DESC";
    $stmt_publications_publiques = $connexion->prepare($sql_publications_publiques);
    $stmt_publications_publiques->execute();

    // Stockage des publications publiques dans la session
    $_SESSION['publications_publiques'] = $stmt_publications_publiques->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des publications publiques : " . $e->getMessage();
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title>Harmony Network</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css"/>
    <script src="./scripts.js"></script>
    <link rel="icon" href="../images/logo_harmony_couleur.png" type="image/png">
</head>
<body>
    <span class="flex-page">
        <!--  MENU DE GAUCHE  -->
          <div class="menu_gauche" <?php if ($is_admin == 1): ?>style="background: url('../images/cadenas_admin.png');background-position: center; background-repeat: no-repeat; background-size: cover;"<?php endif; ?>>
            <img src="../images/logo_harmony.png" alt="logo" class="logo" onclick="accueil_click()">
            <p class="nom_site">Harmony</p>

            <div class="image_menu"><img src="../images/home.png" alt="account" class="images_menu_gauche" onclick="accueil_click()">
                <div class="description_actions"><p>Accueil</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/account.png" alt="account" class="images_menu_gauche" onclick="account_click()">
                <div class="description_actions"><p>Mon profil</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/rechercher.png" alt="rechercher" class="images_menu_gauche" onclick="search_click()">
                <div class="description_actions"><p>Rechercher</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/message.png" alt="messages" class="images_menu_gauche" onclick="messages_click()">
                <div class="description_actions"><p>Messages</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/planning.png" alt="planning" class="images_menu_gauche" onclick="publications_click()">
                <div class="description_actions"><p>Publications</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/settings.png" alt="settings" class="images_menu_gauche" onclick="settings_click()">
                <div class="description_actions"><p>Paramètres</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/exit.png" alt="logout" class="images_menu_gauche" onclick="logout()">
                <div class="description_actions"><p>Déconnexion</p></div>
            </div>
        </div>

        <!--  BLOC CENTRAL  -->
        <div class="bloc_principal">
            <h1>Votre fil'harmony</h1>
            <h2>
                <?php echo "@" . $nom_utilisateur; ?>
            </h2>

            <!--  BLOC DE POST -->
            <div class="harmony_want_post">
                <div class="harmony_zone_ecriture_want_post">
                    <form action="./controleur/traitement_publication.php" method="post">
                        <textarea name="message" id="message" placeholder="Envie d'harmoniser ?" required></textarea>
                        <textarea name="message2" id="message2" style="display: none;"></textarea>
                        <span class="config_post_harmo">
                            <div class="partie_gauche_post">
                                <img src="../images/image.png" alt="image" class="image" onclick="chargerImagePubli()">
                                <img src="../images/emoji.png" alt="emoji" class="emoji">
                                <script>
                                    window.addEventListener('load', setupEmojiSelector);
                                </script>
                            </div>
                            <div class="partie_droite_post">
                                <label for="prive">Privé :</label>
                                <input type="checkbox" name="prive" id="prive" value="prive">
                                <button class="publier_post" type="submit" class="button_base">Publier</button>
                            </div>
                        </span>
                    </form>
                </div>
            </div>
            <br>

          <!-- BLOC DES PUBLICATIONS RECENTES -->
          <div class="zone_titre">
              <p><span>Harmonisations récentes privées de vos amis</span></p>
          </div>
            <br>
            <?php
            // Récupération des utilisateurs auxquels l'utilisateur courant est abonné
            $sql_abonnements = "SELECT id_abonnement FROM abonnements WHERE id_utilisateur = :utilisateur_id";
            $stmt_abonnements = $connexion->prepare($sql_abonnements);
            $stmt_abonnements->bindParam(':utilisateur_id', $_SESSION['id']);
            $stmt_abonnements->execute();
            $abonnements = $stmt_abonnements->fetchAll(PDO::FETCH_COLUMN);

  
          // Vérifier s'il y a des abonnements avant de récupérer les publications privées
          if (!empty($abonnements)) {
              // Récupération des publications privées des utilisateurs abonnés
              $sql_publications_privees_amis = "SELECT * FROM publications WHERE user_id IN (" . implode(",", $abonnements) . ") AND public = 0 ORDER BY date_post DESC";
              $stmt_publications_privees_amis = $connexion->prepare($sql_publications_privees_amis);
              $stmt_publications_privees_amis->execute();
              $publications_privees_amis = $stmt_publications_privees_amis->fetchAll(PDO::FETCH_ASSOC);
            
            $postPrivCount = 0;
            foreach ($publications_privees_amis as $publication_privee_amis):
                // Récupérer le nom d'utilisateur associé à l'ID d'utilisateur du post privé
                $id_utilisateur_prive_amis = $publication_privee_amis['user_id'];
                $sql_utilisateur_prive_amis = "SELECT nom_utilisateur FROM utilisateur WHERE id = :id_utilisateur_prive_amis";
                $stmt_utilisateur_prive_amis = $connexion->prepare($sql_utilisateur_prive_amis);
                $stmt_utilisateur_prive_amis->bindParam(':id_utilisateur_prive_amis', $id_utilisateur_prive_amis);
                $stmt_utilisateur_prive_amis->execute();
                $resultat_utilisateur_prive_amis = $stmt_utilisateur_prive_amis->fetch(PDO::FETCH_ASSOC);
  
                // Récupérer les nouveaux compteurs de likes et dislikes depuis la base de données
                $likesSql = "SELECT SUM(compteur_like) AS totalLikes, SUM(compteur_dislike) AS totalDislikes FROM likes WHERE id_publication = :postId";
                $likesStmt = $connexion->prepare($likesSql);
                $likesStmt->bindParam(':postId', $publication_privee_amis['id']);
                $likesStmt->execute();
                $likesData = $likesStmt->fetch(PDO::FETCH_ASSOC);
  
                // Mettre à jour le nombre de likes et dislikes affiché sur la page
                $likeCount = $likesData['totalLikes'];
                $dislikeCount = $likesData['totalDislikes'];
                ?>
  
                <!-- Afficher chaque publication privée des utilisateurs abonnés -->
                <div class="harmony_post_conf" style="transform:scale(0.95);">
                    <span class="espace_droit_post"></span>
                    <?php echo "Par " ?>
                    <a href="./utilisateur_ext.php?id=<?php echo $id_utilisateur_prive_amis; ?>" style="color:white;">
                      <?php echo "@" . $resultat_utilisateur_prive_amis['nom_utilisateur']; ?>
                    </a>
                    <?php echo "|" ?>
                    <?php echo date('d/m/Y à H\hi', strtotime($publication_privee_amis['date_post'])); ?>
                    <?php echo "| en Privé 🔒" ?>
                    <br><br>
                    <div class="afficheur_poste_harmony">
                        <p><?php echo $publication_privee_amis['contenu']; ?></p>
                    </div>
                    <span class="boutons_likes">
                        <img src="../images/like_bouton.png" alt="Like" class="btn_like_dis" onclick="toggleLike(<?php echo $publication_privee_amis['id']; ?>, 'like')">
                        <br>
                        <span id="likeCount_<?php echo $publication_privee_amis['id']; ?>"> <?php echo $likeCount; ?> </span>
                        <img src="../images/dislike_bouton.png" alt="Dislike" class="btn_like_dis" onclick="toggleLike(<?php echo $publication_privee_amis['id']; ?>, 'dislike')">
                        <br>
                        <span id="dislikeCount_<?php echo $publication_privee_amis['id']; ?>"> <?php echo $dislikeCount; ?> </span>
                    </span>
                </div>
                <br>
  
              <?php
                  $postPrivCount++;
                  if ($postPrivCount >= 4) {
                      break; // Limiter à 4 publications
                  }
              endforeach;
              } else {
                  // Aucun abonnement
                  echo "<p>Aucune publication privée d'amis à afficher.</p>";
              }
              ?>
         

          

          <div class="zone_titre">
              <p><span>Harmonisations récentes publiques de la communauté</span></p>
          </div>
          <br>

            <!--  5 DERNIERS POSTS DE LA COMMUNAUTE  -->
            <?php
            $postCount = 0;
            foreach ($_SESSION['publications_publiques'] as $publication_publique):
                // Ignorer cette publication si elle appartient à l'utilisateur courant
                if ($publication_publique['user_id'] == $_SESSION['id']) {
                    continue;
                }

                // Récupérer les nouveaux compteurs de likes et dislikes depuis la base de données
                $likesSql = "SELECT SUM(compteur_like) AS totalLikes, SUM(compteur_dislike) AS totalDislikes FROM likes WHERE id_publication = :postId";
                $likesStmt = $connexion->prepare($likesSql);
                $likesStmt->bindParam(':postId', $publication_publique['id']);
                $likesStmt->execute();
                $likesData = $likesStmt->fetch(PDO::FETCH_ASSOC);

                // Mettre à jour le nombre de likes et dislikes affiché sur la page
                $likeCount = $likesData['totalLikes'];
                $dislikeCount = $likesData['totalDislikes'];

                // Récupérer le nom d'utilisateur associé à l'ID d'utilisateur du post public
                $id_utilisateur_public = $publication_publique['user_id'];
                $sql_utilisateur_public = "SELECT nom_utilisateur FROM utilisateur WHERE id = :id_utilisateur_public";
                $stmt_utilisateur_public = $connexion->prepare($sql_utilisateur_public);
                $stmt_utilisateur_public->bindParam(':id_utilisateur_public', $id_utilisateur_public);
                $stmt_utilisateur_public->execute();

                // Récupérer le résultat
                $resultat_utilisateur_public = $stmt_utilisateur_public->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="harmony_post_conf" style="transform:scale(0.95);">
                    <span class="espace_droit_post"></span>
                    <?php echo "Par " ?>
                    <a href="./utilisateur_ext.php?id=<?php echo $id_utilisateur_public; ?>" style="color:white;">
                        <?php echo "@".$resultat_utilisateur_public['nom_utilisateur']; ?>
                    </a>
                    <?php echo "|" ?>
                    <?php echo date('d/m/Y à H\hi', strtotime($publication_publique['date_post'])); ?>
                    <?php echo "| en Publique 🌐" ?>
                    <br><br>
                    <div class="afficheur_poste_harmony">
                        <p><?php echo $publication_publique['contenu']; ?></p>
                    </div>
                    <span class="boutons_likes">
                        <img src="../images/like_bouton.png" alt="Like" class="btn_like_dis"
                            onclick="toggleLike(<?php echo $publication_publique['id']; ?>, 'like')">
                        <br>
                        <span id="likeCount_<?php echo $publication_publique['id']; ?>"> <?php echo $likeCount; ?> </span>
                        <img src="../images/dislike_bouton.png" alt="Dislike" class="btn_like_dis"
                            onclick="toggleLike(<?php echo $publication_publique['id']; ?>, 'dislike')">
                        <br>
                        <span id="dislikeCount_<?php echo $publication_publique['id']; ?>"> <?php echo $dislikeCount; ?> </span>
                    </span>
                </div>
                <br>

                <?php
                $postCount++;
                if ($postCount >= 4) {
                    break; // Limiter à 4 publications
                }
            endforeach;
            ?>
        </div>
    </div>
</span>

</body>
</html>
