<?php
  session_start();
?>

<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Harmony Network - Inscription</title>
  <link rel="stylesheet" type="text/css" href="../styles/style.css"/>
  <link rel="stylesheet" type="text/css" href="../styles/style_page_inscription.css"/>
  <link rel="icon" href="../images/logo_harmony_couleur.png" type="image/png">
</head>
<body>
  <span class="flex-page">
  
  <!--  BLOC CENTRAL  -->
  <div class="bloc_principal" id="bloc_principal_inscription">
    
  <h1>Inscription à Harmony Network</h1>
  <h2>Remplissez le formulaire ci-dessous pour créer votre compte.</h2>

  <!-- Formulaire d'inscription -->
    <form action="./controleur/traitement_inscription.php" method="post" enctype="multipart/form-data">
      <label for="email" class="elements_inscription">Adresse e-mail :</label>
      <input type="email" class="elements_inscription" id="email" name="email" required>

      <br><br>

      <label for="password" class="elements_inscription">Mot de passe :</label>
      <input type="password" class="elements_inscription" id="password" name="password" required>

      <br><br>
        
      <label for="username" class="elements_inscription">Nom d'utilisateur :</label>
      <input type="text" class="elements_inscription" id="username" name="username" pattern="[a-zA-Z0-9._]+" title="Utilisez uniquement des lettres, des chiffres, des points (.) ou des soulignés (_)" required>

      <br><br>

      <label for="firstName" class="elements_inscription">Prénom :</label>
      <input type="text" class="elements_inscription" id="firstName" name="firstName" required>

      <br><br>

      <label for="lastName" class="elements_inscription">Nom :</label>
      <input type="text" class="elements_inscription" id="lastName" name="lastName" required>

      <br><br>

      <label for="phone" class="elements_inscription">Téléphone portable :</label>
      <input type="tel" class="elements_inscription" id="phone" name="phone" pattern="^\+33(\s?\d){9}$" placeholder="XXXXXXXXX" value="+33">

      <br><br>

      <label for="birthdate" class="elements_inscription">Date de naissance :</label>
      <input type="date" class="elements_inscription" id="birthdate" name="birthdate" required>

      <br><br>

      <!-- Bouton de soumission du formulaire -->
      <input type="submit" value="S'inscrire" id="btn_inscription">
    </form>
    <br>
    <a href="./login.php">Se connecter</a>

    <br>
  </div>
  </span>
</body>
</html>
