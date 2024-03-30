<?php
session_start(); // Démarre la session

if (!isset($_SESSION['id'])) {
    // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
    header("Location: ./login.php");
    exit();
}


$nom_utilisateur = isset($_SESSION['nom_utilisateur']) ? $_SESSION['nom_utilisateur'] : '';
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : '';
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
$bio = isset($_SESSION['biographie']) ? $_SESSION['biographie'] : '';
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0;
$nombre_abonnes = isset($_SESSION['nombre_abonnes']) ? $_SESSION['nombre_abonnes'] : 0;
$nombre_abonnements = isset($_SESSION['nombre_abonnements']) ? $_SESSION['nombre_abonnements'] : 0;
$abonnements = isset($_SESSION['abonnements']) ? $_SESSION['abonnements'] : array();
$abonnes = isset($_SESSION['abonnes']) ? $_SESSION['abonnes'] : array();

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

// Récupère les publications de l'utilisateur depuis la base de données
try {
    $id_utilisateur = $_SESSION['id'];
    $sql_publications = "SELECT *, (SELECT SUM(compteur_like) FROM likes WHERE id_publication = publications.id) AS totalLikes, (SELECT SUM(compteur_dislike) FROM likes WHERE id_publication = publications.id) AS totalDislikes FROM publications WHERE user_id = :id_utilisateur ORDER BY date_post DESC";
    $stmt_publications = $connexion->prepare($sql_publications);
    $stmt_publications->bindParam(':id_utilisateur', $id_utilisateur);
    $stmt_publications->execute();

    // Stocke les publications dans la session
    $_SESSION['publications'] = $stmt_publications->fetchAll(PDO::FETCH_ASSOC);

    // Ferme le curseur pour éviter les problèmes potentiels
    $stmt_publications->closeCursor();
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des publications : " . $e->getMessage();
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
        <!--  MENU DE GAUCHE  -->
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
            <div class="image_menu"><img src="../images/planning.png" alt="planning" class="images_menu_gauche" onclick="publications_click()"><div class="description_actions"><p>Publications</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/settings.png" alt="settings" class="images_menu_gauche"
                    onclick="settings_click()"><div class="description_actions"><p>Paramètres</p></div></div>
            <br>
            <div class="image_menu"><img src="../images/exit.png" alt="logout" class="images_menu_gauche"
                    onclick="logout()"><div class="description_actions"><p>Déconnexion</p></div></div>
        </div>

        <!--  BLOC CENTRAL  -->
        <div class="bloc_principal">
            <h1>
                <?php echo $prenom . " " . $nom; ?>
            </h1>
            <h2>
                <?php echo "@" . $nom_utilisateur; ?>
            </h2>
            <h3>
                <?php echo $bio; ?>
            </h3>

            <br>

            <span class="abos" onclick="afficherPopupAbo()">
                <?php echo $nombre_abonnes . " Abonnés | " . $nombre_abonnements . " Abonnements"; ?>
            </span>
            <div class="overlay" id="overlay" onclick="fermerPopupAbo()"></div>
            <div id="popup" class="popup">
                <span class="fermer" onclick="fermerPopupAbo()">&times;</span>
                <h2>Abonnés</h2>
                <?php foreach ($abonnes as $abonne): ?>
                <p><?php echo "@" . $abonne; ?></p>
                <?php endforeach; ?>
                <br>
                <h2>Abonnements</h2>
                <?php foreach ($abonnements as $abonnement): ?>
                <p><?php echo "@" . $abonnement; ?></p>
                <?php endforeach; ?>
            </div>

            <br><br>

            <!--  Vos publications  -->
            <div class="zone_titre">
                <p><span>Mes dernières harmonisations</span></p>
            </div>

            <br><br>

            <?php
            date_default_timezone_set('Europe/Paris');
            foreach ($_SESSION['publications'] as $publication): ?>
            <div class="harmony_post_conf">
                <span class="espace_droit_post"></span>
              <?php echo "Par " ?>
              <a href="" style="color:white;">
                <?php echo "@" . $nom_utilisateur; ?>
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
                      <textarea id="commentaire_<?php echo $publication['id']; ?>"
                          placeholder="Ajouter un commentaire"></textarea>
                      <br><br>
                      <button onclick="commenter(<?php echo $publication['id']; ?>)">Ajouter le commentaire</button>
                  </div>
                </div>
                <br>
                <!-- Affiche les commentaires -->
                
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
                    $is_comment_owner = ($id_personne_commente == $_SESSION['id']); // Vérifie si l'utilisateur connecté est l'auteur du commentaire

                    echo "<div class='commentaire'>
                        <span class='commentaire-utilisateur'>
                            Par ";

                    // Condition pour vérifier si le commentaire appartient à l'utilisateur courant
                    if ($is_comment_owner) {
                        echo "<a href='./account.php' style='color:white;'>@{$user['nom_utilisateur']}</a>";
                    } else {
                        echo "<a href='./utilisateur_ext.php?id={$user['id']}' style='color:white;'>@{$user['nom_utilisateur']}</a>";
                    }

                    echo "</span>
                        <span class='commentaire-date'> | {$date_commentaire}</span>
                        <div class='flexcomm'>
                            <p>{$commentaire['contenu']}</p>";
    


                  // Ajout du bouton de suppression uniquement si l'utilisateur connecté est l'auteur du commentaire
                  if ($is_comment_owner) {
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
                    <span
                        id="dislikeCount_<?php echo $publication['id']; ?>"><?php echo $publication['totalDislikes']; ?></span>
                    <form action="./controleur/supprimer_post.php" method="post" class="delete-post-form">
                        <input type="hidden" name="id_post" value="<?php echo $publication['id']; ?>">
                        <img src="../images/poubelle.png" alt="Supprimer Post" id="poubelle" class="btn_like_dis"
                            onclick="delete_post(this)">
                    </form>
                </span>
            </div>
            <br>
            <?php endforeach; ?>
        </div>
    </div>
    </span>
</body>

</html>
