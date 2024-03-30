<?php
session_start();

// Connextion à la base de données
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

if (!isset($_GET['id'])) {
    header("Location: ./recherche.php");
    exit();
}

$id_utilisateur_ext = $_GET['id'];
$is_admin = $_SESSION['is_admin'];
$id_utilisateur_connecte = $_SESSION['id'];

$sql_info_utilisateur = "SELECT * FROM utilisateur WHERE id = :id_utilisateur_ext";
$stmt_info_utilisateur = $connexion->prepare($sql_info_utilisateur);
$stmt_info_utilisateur->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
$stmt_info_utilisateur->execute();

$info_utilisateur = $stmt_info_utilisateur->fetch(PDO::FETCH_ASSOC);

$sql_abonnements = "SELECT id_abonnement FROM abonnements WHERE id_utilisateur = :id_utilisateur_ext";
$stmt_abonnements = $connexion->prepare($sql_abonnements);
$stmt_abonnements->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
$stmt_abonnements->execute();
$abonnements = $stmt_abonnements->fetchAll(PDO::FETCH_COLUMN);

$sql_abonnes = "SELECT id_utilisateur FROM abonnements WHERE id_abonnement = :id_utilisateur_ext";
$stmt_abonnes = $connexion->prepare($sql_abonnes);
$stmt_abonnes->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
$stmt_abonnes->execute();
$abonnes = $stmt_abonnes->fetchAll(PDO::FETCH_COLUMN);

$sql_publications = "SELECT *, (SELECT SUM(compteur_like) FROM likes WHERE id_publication = publications.id) AS totalLikes, (SELECT SUM(compteur_dislike) FROM likes WHERE id_publication = publications.id) AS totalDislikes FROM publications WHERE user_id = :id_utilisateur_ext ORDER BY date_post DESC";
$stmt_publications = $connexion->prepare($sql_publications);
$stmt_publications->bindParam(':id_utilisateur_ext', $id_utilisateur_ext);
$stmt_publications->execute();

$_SESSION['publications'] = $stmt_publications->fetchAll(PDO::FETCH_ASSOC);
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
          <div class="menu_gauche" <?php if ($is_admin == 1): ?>style="background: url('../images/cadenas_admin.png');background-position: center; background-repeat: no-repeat; background-size: cover;"<?php endif; ?>>
            <img src="../images/logo_harmony.png" alt="logo" class="logo" onclick="accueil_click()">
            <p class="nom_site">Harmony</p>

            <div class="image_menu"><img src="../images/home.png" alt="account" class="images_menu_gauche"
                    onclick="accueil_click()"><div class="description_actions"><p>Accueil</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/account.png" alt="account" class="images_menu_gauche"
                    onclick="account_click()"><div class="description_actions"><p>Mon profil</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/rechercher.png" alt="rechercher" class="images_menu_gauche"
                    onclick="search_click()"><div class="description_actions"><p>Rechercher</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/message.png" alt="messages" class="images_menu_gauche" onclick="messages_click()">
                <div class="description_actions"><p>Messages</p></div>
            </div>
            <br>
            <div class="image_menu"><img src="../images/planning.png" alt="planning" class="images_menu_gauche" onclick="publications_click()"><div
                    class="description_actions"><p>Publications</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/settings.png" alt="settings" class="images_menu_gauche"
                    onclick="settings_click()"><div class="description_actions"><p>Paramètres</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/exit.png" alt="logout" class="images_menu_gauche"
                    onclick="logout()"><div class="description_actions"><p>Déconnexion</p></div></div>
        </div>

        <div class="bloc_principal">
          
            <h1>
                <?php echo $info_utilisateur['prenom'] . " " . $info_utilisateur['nom']; ?>
            </h1>
            <h2>
                <?php echo "@" . $info_utilisateur['nom_utilisateur']; ?>
            </h2>
            <h3>
                <?php echo $info_utilisateur['biographie']; ?>
            </h3>

            <br>

            <span class="abos" onclick="afficherPopupAbo()">
                <?php echo count($abonnes) . " Abonnés | " . count($abonnements) . " Abonnements"; ?>
            </span>
            <div class="overlay" id="overlay" onclick="fermerPopupAbo()"></div>
            <div id="popup" class="popup">
                <span class="fermer" onclick="fermerPopupAbo()">&times;</span>
                <h2>Abonnés</h2>
                <?php foreach ($abonnes as $abonne_id): ?>
                    <?php
                    $sql_info_abonne = "SELECT * FROM utilisateur WHERE id = :id_abonne";
                    $stmt_info_abonne = $connexion->prepare($sql_info_abonne);
                    $stmt_info_abonne->bindParam(':id_abonne', $abonne_id);
                    $stmt_info_abonne->execute();
                    $abonne_info = $stmt_info_abonne->fetch(PDO::FETCH_ASSOC);

                    if ($abonne_info) {
                        echo "<p>@" . $abonne_info['nom_utilisateur'] . "</p>";
                    }
                    ?>
                <?php endforeach; ?>
                <br>
                <h2>Abonnements</h2>
                <?php foreach ($abonnements as $abonnement_id): ?>
                    <?php
                    $sql_info_abonnement = "SELECT * FROM utilisateur WHERE id = :id_abonnement";
                    $stmt_info_abonnement = $connexion->prepare($sql_info_abonnement);
                    $stmt_info_abonnement->bindParam(':id_abonnement', $abonnement_id);
                    $stmt_info_abonnement->execute();
                    $abonnement_info = $stmt_info_abonnement->fetch(PDO::FETCH_ASSOC);

                    if ($abonnement_info) {
                        echo "<p>@" . $abonnement_info['nom_utilisateur'] . "</p>";
                    }
                    ?>
                <?php endforeach; ?>
            </div>
            <span class="espace_droit_abo"></span>
            <span class="abos" onclick="gererAbonnement(<?php echo $id_utilisateur_ext; ?>)">
                <?php
                $est_abonne = in_array($id_utilisateur_connecte, $abonnes);
                echo $est_abonne ? "Se désabonner" : "S'abonner";
                ?>
            </span>

            <br><br>

            <!-- POUR l'ADMINISTRATEUR -->
              <?php if ($is_admin == 1): ?>
                  <div class="zone_titre">
                      <p><span>Commandes d'administrateur</span></p>
                  </div>
                  <br>
                  <form action="./controleur/supprimer_utilisateur.php" method="post" class="delete-user-form">
                      <input type="hidden" name="id_utilisateur_ext" value="<?php echo $id_utilisateur_ext; ?>">
                      <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer l'utilisateur</button>
                  </form>
                  <br><br>
              <?php endif; ?>
             <!-- ----------------------- -->

            <div class="zone_titre">
                <p><span>Dernières harmonisations</span></p>
            </div>

            <br><br>

            <?php
            $is_administr = ($is_admin == 1);
            date_default_timezone_set('Europe/Paris');
            foreach ($_SESSION['publications'] as $publication):
                if ($publication['public'] == 1 || $est_abonne || $is_administr):
            ?>
                    <div class="harmony_post_conf">
                        <span class="espace_droit_post"></span>
                        <?php echo "Par " ?>
                        <a href="" style="color:white;">
                          <?php echo "@" . $info_utilisateur['nom_utilisateur']; ?>
                        </a>
                        <?php echo "|" ?>
                        <?php echo date('d/m/Y à H\hi', strtotime($publication['date_post'])); ?>
                        <?php echo "| en" ?>
                        <?php echo $publication['public'] == 0 ? "Privé 🔒" : "Publique 🌐"; ?>
                        <br><br>
                        <div class="afficheur_poste_harmony">
                            <p><?php echo $publication['contenu']; ?></p>
                        </div>
                        <!-- Affiche le formulaire de commentaire -->
                        <br>
                        <div class="btn_commenter">
                            <button onclick="toggleCommentForm(<?php echo $publication['id']; ?>)">Commenter</button>
                        </div>
                        <div class="zone_comm">
                            <div id="commentForm_<?php echo $publication['id']; ?>" style="display: none;" class="txt_com">
                                <textarea id="commentaire_<?php echo $publication['id']; ?>" placeholder="Ajouter un commentaire"></textarea>
                                <br><br>
                                <button onclick="commenter(<?php echo $publication['id']; ?>)">Ajouter le commentaire</button>
                            </div>
                        </div>
                        <br>
                      
                      <!-- Affiche les commentaires -->
                      <?php
                      $id_publication = $publication['id'];
                      $sql_commentaires = "SELECT * FROM commentaires WHERE id_publication = :id_publication ORDER BY date_commentaire DESC";
                      $stmt_commentaires = $connexion->prepare($sql_commentaires);
                      $stmt_commentaires->bindParam(':id_publication', $id_publication);
                      $stmt_commentaires->execute();
                      $commentaires = $stmt_commentaires->fetchAll(PDO::FETCH_ASSOC);
                      echo "<div class='affichage_comm'>";
                      foreach ($commentaires as $commentaire) {
                          $id_personne_commente = $commentaire['id_personne_commente'];
                          $sql_user = "SELECT id, nom_utilisateur FROM utilisateur WHERE id = :id_personne_commente";
                          $stmt_user = $connexion->prepare($sql_user);
                          $stmt_user->bindParam(':id_personne_commente', $id_personne_commente);
                          $stmt_user->execute();
                          $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

                          $date_commentaire = date('d/m/Y à H\hi', strtotime($commentaire['date_commentaire']));
                          $is_comment_owner = ($id_personne_commente == $id_utilisateur_connecte);

                          echo "<div class='commentaire'>
                                  <span class='commentaire-utilisateur'>
                                      Par ";
                          // condition pour le lien en fonction de l'utilisateur ou de l'administrateur
                          if ($is_comment_owner) {
                            echo "<a href='./account.php' style='color:white;'>@{$user['nom_utilisateur']}</a></span>";
                          } else {
                              echo "<a href='./utilisateur_ext.php?id={$user['id']}' style='color:white;'>@{$user['nom_utilisateur']}</a></span>";
                          }
                          echo "
                                  <span class='commentaire-date'> | {$date_commentaire}</span>
                                  <div class='flexcomm'>
                                      <p>{$commentaire['contenu']}</p>";

                          // bouton de suppression uniquement si l'utilisateur connecté est l'auteur du commentaire ou est administrateur
                          if ($is_comment_owner || $is_administr) {
                              echo "<button onclick='deleteComment({$commentaire['id']})'>❌</button>";
                          }

                          echo "</div></div>";
                      }
                      echo "</div>";
                      ?>

                        <span class="boutons_likes">
                            <img src="../images/like_bouton.png" alt="Like" class="btn_like_dis"
                                onclick="toggleLike(<?php echo $publication['id']; ?>, 'like')">
                            <br>
                          <span id="likeCount_<?php echo $publication['id']; ?>"><?php echo $publication['totalLikes']; ?></span>
                            <img src="../images/dislike_bouton.png" alt="Dislike" class="btn_like_dis"
                                onclick="toggleLike(<?php echo $publication['id']; ?>, 'dislike')">
                            <br>
                          <span id="dislikeCount_<?php echo $publication['id']; ?>"><?php echo $publication['totalDislikes']; ?></span>
                            <?php if ($id_utilisateur_connecte == $id_utilisateur_ext || $is_administr): ?>
                                <form action="./controleur/supprimer_post.php" method="post"
                                    class="delete-post-form">
                                    <input type="hidden" name="id_post"
                                        value="<?php echo $publication['id']; ?>">
                                    <img src="../images/poubelle.png" alt="Supprimer Post" id="poubelle"
                                        class="btn_like_dis" onclick="delete_post(this)">
                                </form>
                            <?php endif; ?>
                        </span>
                    </div>
                    <br>
            <?php
                endif;
            endforeach;
            ?>
        </div>
    </span>
</body>

</html>
