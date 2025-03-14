<?php

// Vérifie si le formulaire a été soumis et si le champ caché "sent" contient "ok"
if (isset($_POST["sent"]) && $_POST["sent"] == "ok") {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php'; // Contient des fonctions utilitaires
  // Inclut le fichier de connexion à la base de données
  require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";

  // Prépare une requête SQL pour rechercher un utilisateur dans la table "table_admin" avec l'email fourni
  $stmt = $db->prepare("SELECT * FROM table_admin WHERE admin_mail=:mail");

  // Lie la valeur de l'email envoyé via le formulaire au paramètre :mail de la requête SQL
  $stmt->bindValue(":mail", $_POST["admin_mail"]);

  // Exécute la requête SQL préparée
  $stmt->execute();

  // Vérifie si un utilisateur avec cet email a été trouvé
  if ($row = $stmt->fetch()) {

    // Vérifie si le mot de passe saisi par l'utilisateur correspond au mot de passe haché dans la base de données
    if (password_verify($_POST["admin_password"], $row["admin_password"])) {

      // Démarre une session pour l'utilisateur
      session_start();

      // Définit une variable de session pour indiquer que l'utilisateur est connecté
      $_SESSION['is_logged'] = "oui";

      // Redirige l'utilisateur vers la page "index.php" après une connexion réussie
      header("location:/admin/product/index.php");

      // Terminer l'exécution du script après la redirection
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Login</title>
</head>

<body>
  <form action="login.php" method="post">
    <label for="admin_mail">Email: </label>
    <input type="email" id="admin_mail" name="admin_mail" required>
    <label for="admin_password">Mot de passe</label>
    <input type="password" id="admin_password" name="admin_password" required>
    <input type="hidden" name="sent" value="ok">
    <input type="submit" value="Connexion">
  </form>
</body>

</html>