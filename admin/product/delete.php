<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php';
// Inclusion du fichier de protection 'protect.php' qui vérifie l'authentification de l'utilisateur
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php';
// Inclusion du fichier de connexion 'connect.php' qui établit la connexion à la base de données
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/connect.php';

// Vérification que le paramètre 'id' est présent dans l'URL et qu'il est un nombre valide
if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    // Préparation de la requête SQL pour supprimer un produit en fonction de son ID
    $stmt = $db->prepare("DELETE FROM table_product WHERE product_id= :id");

    // Exécution de la requête préparée en liant le paramètre ':id' à la valeur de 'id' passé dans l'URL
    $stmt->execute([":id" => $_GET["id"]]);

    // Si la requête est exécutée avec succès, l'enregistrement est supprimé de la table 'table_product'
}
// Redirection vers la page index.php après la suppression du produit
redirect("/admin/product/index.php")
    ?>