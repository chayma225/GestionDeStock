<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Finalement, détruire la session.
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header("Location: ../frontend/login.html");
exit();
?>
