<?php
// Démarre la session. Cela permet d'accéder aux variables de session
session_start();

// Vérifie si la variable de session 'is_logged' n'est pas définie ou si sa valeur n'est pas égale à "oui"
if (!isset($_SESSION['is_logged']) || $_SESSION['is_logged'] != "oui") {
  // Si l'utilisateur n'est pas connecté, il est redirigé 
  redirect("/admin/login.php");
  // Utilisation de 'exit' pour arrêter l'exécution du script après la redirection
  exit;
}
if (empty($_SESSION['token'])) {
  $_SESSION['token'] = md5(date("Ymdhis"));
}
if (isset($_POST['token']) && $_POST['token'] != $_SESSION['token']) {
  $_SESSION['is_logged'] = "no";
  redirect("/admin/login.php");
}
if (isset($_GET['token']) && $_GET['token'] != $_SESSION['token']) {
  $_SESSION['is_logged'] = "no";
  redirect("/admin/login.php");
}

// À partir de ce moment, l'utilisateur est considéré comme connecté et peut accéder à la page protégée

?>