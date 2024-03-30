<?php
session_start(); // Démarre la session

// Détruit toutes les données de session
session_destroy();

// Redirige vers la page de connexion
header("Location: ./login.php");
exit();
?>