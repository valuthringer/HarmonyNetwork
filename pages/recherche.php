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

// Traitement de la recherche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recherche = $_POST['recherche'];

    // recherche imprécise
    $sql_recherche = "SELECT id, nom_utilisateur FROM utilisateur WHERE nom_utilisateur LIKE :recherche";
    $stmt_recherche = $connexion->prepare($sql_recherche);
    $stmt_recherche->bindValue(':recherche', '%' . $recherche . '%');
    $stmt_recherche->execute();

    // Récupérer les résultats
    $resultats = $stmt_recherche->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Rechercher un utilisateur</h1>
        <br> <br>
        <!-- Formulaire de recherche -->
        <form action="" method="post">
            <label for="recherche">Nom d'utilisateur :</label>
            <input type="text" name="recherche" id="recherche" required>
            <br><br>
            <button type="submit" class="button_base">Rechercher</button>
        </form>

        <!-- Affichage des résultats de la recherche -->
      <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($resultats)): ?>
          <br>
          <ul class="res_list">
              <?php foreach ($resultats as $resultat): ?>
                  <li>
                      <?php
                      // Vérifie si l'utilisateur connecté clique sur son propre utilisateur
                      if ($resultat['id'] == $_SESSION['id']) {
                          $redirection_url = "./account.php";
                      } else {
                          $redirection_url = "./utilisateur_ext.php?id=" . $resultat['id'];
                      }
                      ?>
                      <a style="color:white;" href="<?php echo $redirection_url; ?>">
                          <?php echo "@" . $resultat['nom_utilisateur']; ?>
                      </a>
                  </li>
              <?php endforeach; ?>
          </ul>
      <?php endif; ?>

    </div>
</span>
</body>
</html>
