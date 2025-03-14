<?php
// Inclusion des fichiers nécessaires
// Inclusion du fichier de fonctions utilitaires 'function.php' pour sécuriser les entrées/sorties utilisateur et d'autres fonctions utiles
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php';
// Inclusion du fichier de protection 'protect.php' pour s'assurer que l'utilisateur est authentifié avant de pouvoir continuer
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php';
// Inclusion du fichier de connexion à la base de données 'connect.php' pour pouvoir interagir avec la base de données
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/connect.php';


// Initialiser un tableau vide pour un produit
// Cela permet d'afficher un formulaire vide si un produit n'est pas sélectionné
$product = [
    "category_id" => "", // Identifiant de la catégorie du produit
    "product_id" => "",             // Identifiant unique du produit
    "product_slug" => "",           // Slug (version simplifiée de l'URL) du produit
    "product_name" => "",           // Nom du produit
    "product_serie" => "",          // Série ou collection à laquelle appartient le produit
    "product_volume" => "",         // Volume ou numéro dans une série de produits
    "product_description" => "",    // Description détaillée du produit
    "product_price" => "",          // Prix du produit
    "product_stock" => "",          // Quantité en stock pour ce produit
    "product_publisher" => "",      // Éditeur du produit
    "product_author" => "",         // Auteur du produit
    "product_cartoonist" => "",     // Dessinateur ou illustrateur (si applicable)
    "product_image" => "",          // Nom de l'image ou chemin vers l'image associée au produit
    "product_resume" => "",         // Résumé ou courte description du produit
    "product_date" => "",           // Date de publication du produit
    "product_status" => "",         // Statut du produit (actif ou inactif)
    "product_type_id" => ""         // Identifiant du type de produit
];

// Vérifier si un ID est passé dans l'URL pour modifier un produit existant
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    // Préparer la requête SQL pour récupérer les informations du produit correspondant à l'ID
    $stmt = $db->prepare("SELECT * FROM table_product WHERE product_id = :id");
    // Exécuter la requête en remplaçant le paramètre :id par l'ID passé dans l'URL
    $stmt->execute([":id" => $_GET["id"]]);
    // Récupérer les informations du produit dans un tableau associatif
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Si le produit n'existe pas, $product sera un tableau vide
}
// Récupérer les catégories pour le menu déroulant
$stmt = $db->prepare("SELECT category_id, category_name FROM table_category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Récupérer tous les types de produits depuis la table `table_type` pour les afficher dans un menu déroulant
$stmt = $db->prepare("SELECT type_id, type_name FROM table_type");
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les types sous forme de tableau associatif
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Titre de la page -->
    <title>Ajouter / Modifier un produit</title>
    <!-- Lien vers le fichier CSS pour la mise en forme de la page -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Formulaire d'ajout ou de modification d'un produit -->
    <form action="process.php" method="post" enctype="multipart/form-data">
        <!-- Champ caché pour l'ID du produit (utilisé pour les modifications) -->
        <input type="hidden" name="product_id" value="<?= hsc($product["product_id"]) ?>">
        <!-- Champ pour la catégorie du produit -->
        <label for="category_id">Catégorie :</label>
        <select name="category_id">
            <option value="">-- Sélectionnez une catégorie --</option>
            <?php
            // Récupérer toutes les catégories disponibles
            $stmt = $db->prepare("SELECT category_id, category_name FROM table_category");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Vérifier que la catégorie du produit est bien définie
            $selectedCategoryId = isset($product['product_type_id']) ? $product['product_type_id'] : ''; // Assurez-vous que le produit a un type de catégorie
            
            // Affichage des catégories avec sélection de la bonne catégorie
            foreach ($categories as $category) {
                echo "<option value='" . hsc($category['category_id']) . "'";

                // Comparer category_id avec la valeur product_type_id (s'il y a un produit à modifier)
                if ($selectedCategoryId == $category['category_id']) {
                    echo " selected"; // Sélectionner la catégorie qui correspond
                }

                echo ">" . hsc($category['category_name']) . "</option>";
            }
            ?>
        </select>
        <!-- Champ pour le slug du produit -->
        <label for="product_slug">Slug : </label>
        <input type="text" name="product_slug" value="<?= hsc($product["product_slug"]) ?>">

        <!-- Champ pour le nom du produit -->
        <label for="product_name">Nom du produit :</label>
        <input type="text" name="product_name" value="<?= hsc($product["product_name"]) ?>" REQUIRED>

        <!-- Champ pour la série du produit -->
        <label for="product_serie">Série : </label>
        <input type="text" name="product_serie" value="<?= hsc($product["product_serie"]) ?>">

        <!-- Champ pour le volume du produit -->
        <label for="product_volume">Volume : </label>
        <input type="text" name="product_volume" value="<?= hsc($product["product_volume"]) ?>">

        <!-- Champ pour la description du produit -->
        <label for="product_description">Description : </label>
        <textarea name="product_description"><?= hsc($product["product_description"]) ?></textarea>

        <!-- Champ pour le prix du produit -->
        <label for="product_price">Prix : </label>
        <input type="number" name="product_price" value="<?= hsc($product["product_price"]) ?>" step="any">

        <!-- Champ pour la quantité en stock -->
        <label for="product_stock">Stock : </label>
        <input type="number" name="product_stock" value="<?= hsc($product["product_stock"]) ?>">

        <!-- Champ pour l'éditeur du produit -->
        <label for="product_publisher">Éditeur : </label>
        <input type="text" name="product_publisher" value="<?= hsc($product["product_publisher"]) ?>">

        <!-- Champ pour l'auteur du produit -->
        <label for="product_author">Auteur : </label>
        <input type="text" name="product_author" value="<?= hsc($product["product_author"]) ?>">

        <!-- Champ pour le dessinateur (illustrateur) du produit -->
        <label for="product_cartoonist">Dessinateur : </label>
        <input type="text" name="product_cartoonist" value="<?= hsc($product["product_cartoonist"]) ?>">

        <!-- Champ pour télécharger l'image du produit -->
        <label for="product_image">Image : </label>
        <input type="file" name="product_image">

        <!-- Champ pour le résumé du produit -->
        <label for="product_resume">Résumé : </label>
        <textarea name="product_resume"><?= hsc($product["product_resume"]) ?></textarea>

        <!-- Champ pour la date de publication du produit -->
        <label for="product_date">Date : </label>
        <input type="date" name="product_date" value="<?= hsc($product["product_date"]) ?>">

        <!-- Champ pour sélectionner le statut du produit (actif ou inactif) -->
        <label for="product_status">Statut : </label>
        <select name="product_status">
            <!-- Sélectionner "Actif" si le produit est actif -->
            <option value="active" <?= $product["product_status"] == "active" ? "selected" : "" ?>>Actif</option>
            <!-- Sélectionner "Inactif" si le produit est inactif -->
            <option value="inactive" <?= $product["product_status"] == "inactive" ? "selected" : "" ?>>Inactif</option>
        </select>

        <!-- Champ pour sélectionner le type du produit dans un menu déroulant -->
        <label for="product_type_id">Type de produit : </label>
        <select name="product_type_id">
            <option value="">-- Sélectionnez un type --</option>
            <?php foreach ($types as $type) { ?>
                <!-- Affichage des types de produit récupérés depuis la base de données -->
                <option value="<?= hsc($type['type_id']); ?>" <?= $product['product_type_id'] == $type['type_id'] ? 'selected' : '' ?>>
                    <?= hsc($type['type_name']); ?>
                </option>
            <?php } ?>
        </select>
        <!-- Champ caché pour indiquer qu'une requête a été envoyée -->
        <input type="hidden" name="sent" value="ok">
        <input type="hidden" name="token" value="<?=$_SESSION['token'];?>">
        <!-- Boutons pour annuler ou enregistrer le produit -->
        <input type="submit" name="action" value="Annuler" formaction="index.php" />
        <input type="submit" value="Enregistrer" />
    </form>
</body>

</html>