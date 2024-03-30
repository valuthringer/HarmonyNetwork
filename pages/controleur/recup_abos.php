<?php
  session_start();
  
  // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=lv200932_projet_r301';
    $user = 'lv200932';
    $password_db = 'lv200932';
  
  try {
      $connexion = new PDO($dsn, $user, $password_db);
      $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
      // Récupère les utilisateurs abonnés au compte courant
      $id_utilisateur_courant = $_SESSION['id'];
      $sql_abonnes = "SELECT utilisateur.nom_utilisateur
                      FROM utilisateur
                      JOIN abonnements ON utilisateur.id = abonnements.id_utilisateur
                      WHERE abonnements.id_abonnement = :id_utilisateur";
      $stmt_abonnes = $connexion->prepare($sql_abonnes);
      $stmt_abonnes->bindParam(':id_utilisateur', $id_utilisateur_courant);
      $stmt_abonnes->execute();
      $abonnes = $stmt_abonnes->fetchAll(PDO::FETCH_COLUMN);
  
      // Récupère les abonnements de l'utilisateur courant
      $sql_abonnements = "SELECT utilisateur.nom_utilisateur
                          FROM utilisateur
                          JOIN abonnements ON utilisateur.id = abonnements.id_abonnement
                          WHERE abonnements.id_utilisateur = :id_utilisateur";
      $stmt_abonnements = $connexion->prepare($sql_abonnements);
      $stmt_abonnements->bindParam(':id_utilisateur', $id_utilisateur_courant);
      $stmt_abonnements->execute();
      $abonnements = $stmt_abonnements->fetchAll(PDO::FETCH_COLUMN);
  
      // Compte le nombre d'abonnés et d'abonnements
      $nombre_abonnes = count($abonnes);
      $nombre_abonnements = count($abonnements);
  
      // Stocke les données dans la session
      $_SESSION['abonnes'] = $nombre_abonnes;
      $_SESSION['abonnements'] = $nombre_abonnements;
  
      // Redirige vers la page courante avec la méthode POST
      header("Location: ./", true, 303);
      exit();
  } catch (PDOException $e) {
      echo "Erreur de connexion : " . $e->getMessage();
  } finally {
      // Ferme la connexion à la base de données
      $connexion = null;
  }
?>
