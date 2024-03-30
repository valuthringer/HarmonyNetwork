<?php
session_start(); // Démarre la session
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    //Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
    header("Location: ./login.php");
    exit();
}

$nom_utilisateur = $_SESSION['nom_utilisateur'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$email = $_SESSION['email'];
$tel = $_SESSION['telephone_portable'];
$naiss = $_SESSION['date_naissance'];
$bio = $_SESSION['biographie'];
$is_admin = $_SESSION['is_admin'];

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
    
    <!--  BLOC CENTRAL  -->
    <div class="bloc_principal">
        <h1>
          Paramètres du compte
        </h1>

        <br>
        <!--  Catégories  -->
        <div class="zone_titre">
            <p><span>Informations liées au compte</span></p>
        </div>
        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Nom d'utilisateur : @".$nom_utilisateur;
          ?>
        </p>
        <button class="button_edit" onclick="edit_nom_utilisateur()">Modifier</button>

        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Prénom : ".$prenom;
          ?>
        </p>
        <button class="button_edit" onclick="edit_prenom()">Modifier</button>

        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Nom : ".$nom;
          ?>
        </p>
        <button class="button_edit" onclick="edit_nom()">Modifier</button>

        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Biographie";
          ?>
        </p>
        <button class="button_edit" onclick="edit_bio()">Modifier</button>
      
        <div class="zone_titre">
            <p><span>Informations personnelles</span></p>
        </div>

        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Email : ".$email;
          ?>
        </p>
        <button class="button_edit" onclick="edit_mail()">Modifier</button>
      
        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Tel : ".$tel;
          ?>
        </p>
        <button class="button_edit" onclick="edit_tel()">Modifier</button>

        <div class="espace"></div>
        <p class="infos_user">
          <?php
            echo "Date naissance : ".$naiss;
          ?>
        </p>
        <button class="button_edit" onclick="edit_naiss()">Modifier</button>

        <div class="espace"></div>
        <br><br>
        <p class="infos_user">
        </p>
        <form action="./controleur/supprimer_son_compte.php" method="post" class="delete-user-form">
            <input type="hidden" name="id_utilisateur_ext" value="<?php echo $id_utilisateur_ext; ?>">
            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')">Supprimer mon compte Harmony</button>
        </form>

      

       <br>

      </div>
    </div>


  </span>

  </body>
</html>