<?php
// Inclusion des fichiers nécessaires
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php'; // Contient des fonctions utilitaires
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php'; // Vérifie que l'utilisateur est authentifié
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/connect.php'; // Connecte à la base de données

// Vérifie si l'action "Annuler" a été envoyée
if (isset($_POST["action"]) && $_POST["action"] === "Annuler") {
    // Si l'action est "Annuler", on redirige vers la page d'index sans enregistrer
    header("Location: index.php");
    exit;
}

// Vérifie si le formulaire a été envoyé
if (isset($_POST["sent"]) && $_POST["sent"] == "ok") {
    // Récupération du nom de l'image
    $product_image = date("Ymdhis") . $_FILES['product_image']['name'];
    // Récupération de toutes les données du formulaire
    $data = [
        ":product_slug" => $_POST["product_slug"], // Slug du produit
        ":product_name" => $_POST["product_name"], // Nom du produit
        ":product_serie" => $_POST["product_serie"], // Série du produit
        ":product_volume" => $_POST["product_volume"], // Volume du produit
        ":product_description" => $_POST["product_description"], // Description détaillée
        ":product_price" => $_POST["product_price"], // Prix du produit
        ":product_stock" => $_POST["product_stock"], // Stock disponible
        ":product_publisher" => $_POST["product_publisher"], // Éditeur
        ":product_author" => $_POST["product_author"], // Auteur
        ":product_cartoonist" => $_POST["product_cartoonist"], // Dessinateur
        ":product_resume" => $_POST["product_resume"], // Résumé du produit
        ":product_date" => $_POST["product_date"], // Date de publication ou ajout
        ":product_status" => $_POST["product_status"], // Statut (actif/inactif)
        ":product_type_id" => $_POST["product_type_id"], // Type de produit (catégorie)
        ":category_id" => $_POST["category_id"], // Catégorie choisie
        ":product_image" => isset($_FILES["product_image"]) ? $product_image : null,
    ];

    // Gestion de l'image si un fichier est téléchargé
    move_uploaded_file($_FILES['product_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/upload/" . $product_image);
    // Vérifie si un ID de produit existe pour déterminer s'il s'agit d'une mise à jour ou d'un ajout
    if (!empty($_POST["product_id"])) {
        // Mise à jour d'un produit existant
        $stmt = $db->prepare("
            UPDATE table_product 
            SET 
                product_slug = :product_slug, 
                product_name = :product_name, 
                product_serie = :product_serie, 
                product_volume = :product_volume, 
                product_description = :product_description, 
                product_price = :product_price, 
                product_stock = :product_stock, 
                product_publisher = :product_publisher, 
                product_author = :product_author, 
                product_cartoonist = :product_cartoonist, 
                product_image = :product_image, 
                product_resume = :product_resume, 
                product_date = :product_date, 
                product_status = :product_status,
                product_type_id = :product_type_id,
                category_id = :category_id 
            WHERE product_id = :product_id
        ");
        $data[":product_id"] = $_POST["product_id"]; // Ajoute l'ID du produit à mettre à jour
    } else {
        // Ajout d'un nouveau produit
        $stmt = $db->prepare("
            INSERT INTO table_product 
            (product_slug, product_name, product_serie, product_volume, product_description, product_price, product_stock, product_publisher, product_author, product_cartoonist, product_image, product_resume, product_date, product_status, product_type_id, category_id)
            VALUES 
            (:product_slug, :product_name, :product_serie, :product_volume, :product_description, :product_price, :product_stock, :product_publisher, :product_author, :product_cartoonist, :product_image, :product_resume, :product_date, :product_status, :product_type_id, :category_id)
        ");
    }
    // Exécution de la requête SQL
    $stmt->execute($data);
}

// Redirection vers la page d'index après l'ajout ou la mise à jour
header("location:index.php");
exit;
?>