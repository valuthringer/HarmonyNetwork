<?php
  session_start();
?>

<html>
  <head>
    <meta charset="utf-8">
    <title>Harmony Network</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css"/>
    <link rel="stylesheet" type="text/css" href="../styles/style_page_login.css"/>
    <script src="./scripts.js"></script>
    <link rel="icon" href="../images/logo_harmony_couleur.png" type="image/png">
  </head>
  <body onload="arrobase_preremplie()">
  <span class="flex-page">

    <!--  BLOC CENTRAL  -->
    <div class="bloc_principal" id="bloc_principal_login">
        <h1>Bienvenue sur Harmony 🖐</h1>
        <h2>Heureux de vous revoir...</h2>

        <h3>S'identifier</h3>
          <?php
          // Affiche le message d'erreur s'il y a une erreur de connexion
          if (isset($_SESSION['login_error'])) {
              echo '<div id="error-message">';
              echo '<p>Utilisateur ou mot de passe incorrect.</p>';
              echo '<span onclick="closeErrorMessageLogin()">&times;</span>';
              echo '</div>';
              unset($_SESSION['login_error']);
              echo '<script>setTimeout(function() { document.getElementById("overlay").classList.add("visible"); }, 0);</script>';
          }
          ?>

      <!-- Formulaire de connexion -->
      <form action="./controleur/traitement_login.php" method="post" onsubmit="removeAtSymbol()">
        <label for="username" class="elements_login">Nom d'utilisateur :</label>
        <input type="text" class="elements_login" id="username" name="username" required>
        <br>
        <div class="espace"></div>
        <label for="password" class="elements_login">Mot de passe :</label>
        <input type="password" class="elements_login" id="password" name="password" required>
        <br>
        <br>
    
        <input type="submit" value="Se connecter" id="btn_connexion">
        <br>
        
      </form>

      <br>
      <a href="./page_inscription.php">S'inscrire</a>

      <br>
      
      
    </div>
  </span>
<div id="overlay" class="<?php echo isset($_SESSION['login_error']) ? 'visible' : ''; ?>"></div>
</body>
</html>
