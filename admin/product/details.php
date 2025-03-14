<?php
// Inclusion des fichiers nécessaires
// Inclusion du fichier de fonctions utilitaires comme 'hsc()' pour sécuriser les sorties
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php';
// Inclusion du fichier de protection 'protect.php' qui vérifie l'authentification de l'utilisateur
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php';
// Inclusion du fichier de connexion 'connect.php' pour établir la connexion à la base de données
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/connect.php';


// Vérification et récupération de l'ID du produit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Requête avec jointure pour récupérer le produit et son type
    $stmt = $db->prepare("
    SELECT p.*, t.type_name, c.category_name
    FROM table_product p
    LEFT JOIN table_type t ON p.product_type_id = t.type_id
    LEFT JOIN table_category c ON p.category_id = c.category_id
    WHERE p.product_id = :id
");
    // Exécution de la requête avec l'ID du produit passé en paramètre
    $stmt->execute([":id" => $_GET['id']]);
    // Récupération des données du produit dans un tableau associatif
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si le produit existe dans la base de données
    if (!$product) {
        // Si le produit n'est pas trouvé, afficher un message d'erreur et arrêter le script
        die("Produit introuvable.");
    }
} else {
    // Si l'ID du produit est invalide (non présent ou non numérique), arrêter le script avec un message d'erreur
    die("ID invalide.");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Titre de la page -->
    <title>Detail du produit</title>
    <!-- Lien vers le fichier CSS pour la mise en forme de la page -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Section affichant les détails du produit -->
    <div class="product-details">
        <!-- Affichage du nom du produit avec protection contre les injections XSS -->
        <h2><?= hsc($product['product_name']) ?></h2>

        <!-- Liste des détails du produit sous forme de description (dl) -->
        <dl>
            <!-- Catégorie du produit -->
            <dt>Catégorie :</dt>
            <dd><?= hsc($product['category_name']) ?></dd>
            <!-- Série du produit -->
            <dt>Série :</dt>
            <dd><?= hsc($product['product_serie']) ?></dd>

            <!-- Volume du produit -->
            <dt>Volume :</dt>
            <dd><?= hsc($product['product_volume']) ?></dd>

            <!-- Description du produit -->
            <dt>Description :</dt>
            <dd><?= hsc($product['product_description']) ?></dd>

            <!-- Prix du produit -->
            <dt>Prix :</dt>
            <dd><?= hsc($product['product_price']) ?> €</dd>

            <!-- Stock disponible du produit -->
            <dt>Stock :</dt>
            <dd><?= hsc($product['product_stock']) ?></dd>

            <!-- Éditeur du produit -->
            <dt>Éditeur :</dt>
            <dd><?= hsc($product['product_publisher']) ?></dd>

            <!-- Auteur du produit -->
            <dt>Auteur :</dt>
            <dd><?= hsc($product['product_author']) ?></dd>

            <!-- Dessinateur du produit -->
            <dt>Dessinateur :</dt>
            <dd><?= hsc($product['product_cartoonist']) ?></dd>

            <!-- Résumé du produit -->
            <dt>Résumé :</dt>
            <dd><?= hsc($product['product_resume']) ?></dd>

            <!-- Date de publication du produit -->
            <dt>Date de publication :</dt>
            <dd><?= hsc($product['product_date']) ?></dd>

            <!-- Statut du produit 
            <dt>Statut :</dt>
            <dd><?= hsc($product['product_status']) ?></dd>-->

            <!-- Type du produit -->
            <dt>Type :</dt>
            <dd><?= hsc($product['type_name']) ?></dd>
        </dl>

        <!-- Lien pour revenir à la liste des produits -->
        <a href="index.php">Retour à la liste</a>
    </div>
</body>

</html>