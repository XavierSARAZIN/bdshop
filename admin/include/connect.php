<?php
// Bloc try...catch pour gérer les erreurs de connexion à la base de données
try {
  // Création d'une instance PDO (PHP Data Objects) pour se connecter à la base de données
  // - 'mysql:host=localhost' : spécifie que le serveur de base de données est local (sur la même machine que le script).
  // - 'dbname=bdshop' : indique le nom de la base de données utilisée (ici "bdshop").
  // - 'charset=utf8' : assure que les échanges de données utilisent l'encodage UTF-8.
  // - 'root' : identifiant de connexion à la base de données (administrateur par défaut pour MySQL).
  // - '' : mot de passe pour l'utilisateur 'root' (ici vide, car souvent par défaut sur les environnements locaux).
  $db = new PDO('mysql:host=localhost;dbname=bdshop;charset=utf8', 'root', '');
  // Activation des erreurs PDO en mode Exception
//  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // Si une erreur survient lors de la connexion, elle est capturée ici
  // 'PDOException' est une exception spécifique à PDO qui contient des informations sur l'erreur.

  // Interrompt le script en affichant un message d'erreur
  // - $e->getMessage() : retourne un message décrivant l'erreur (par exemple "Accès refusé" ou "Base de données introuvable").
  die('Erreur : ' . $e->getMessage());
}
?>