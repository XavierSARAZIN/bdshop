<?php
// Inclusion des fichiers nécessaires (protection, connexion, fonctions)
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/connect.php';


// Initialisation des variables pour la pagination
$perPage = 15; // Nombre d'éléments par page
$page = 1; // Page par défaut

// Récupération de la page actuelle depuis l'URL (paramètre 'p')
if (isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] > 0) {
    $page = $_GET['p']; // Affecte la valeur de 'p' à la variable $page
}

// Préparation de la requête SQL avec pagination
$stmt = $db->prepare("
    SELECT p.*, c.category_name
    FROM table_product p
    LEFT JOIN table_category c ON p.category_id = c.category_id
    ORDER BY product_id DESC
    LIMIT :limit 
    OFFSET :offset
");
$stmt->bindValue(":limit", $perPage, PDO::PARAM_INT); // Limite le nombre de résultats
$stmt->bindValue(":offset", ($page - 1) * $perPage, PDO::PARAM_INT); // Définit l'offset pour la pagination
$stmt->execute();

// Récupération des résultats dans un tableau associatif
$recordset = $stmt->fetchAll();

// Récupération du nombre total de produits pour la légende et la pagination
$stmt = $db->prepare("SELECT COUNT(product_id) AS total FROM table_product");
$stmt->execute();
$row = $stmt->fetch();
$total = $row['total']; // Nombre total de produits dans la base de données

// Calcul du nombre total de pages
$nbPage = ceil($total / $perPage);
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

// Récupérer les catégories pour le menu déroulant
$stmt = $db->prepare("SELECT category_id, category_name FROM table_category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Récupérer tous les types de produits depuis la table `table_type` pour les afficher dans un menu déroulant
$stmt = $db->prepare("SELECT type_id, type_name FROM table_type");
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les types sous forme de tableau associatif


// Récupérer le mot-clé de la recherche
$keyword = $_GET['keyword'] ?? '';

// Préparation des filtres (comme déjà implémenté)
$whereClauses = [];
$params = [];

// Filtrer par mot-clé
if (!empty($keyword)) {
    $keyword = "%" . $keyword . "%";  // Encapsule le mot-clé avec les jokers pour une recherche partielle
    $whereClauses[] = "(p.product_name LIKE :keyword OR p.product_description LIKE :keyword)";
    $params[':keyword'] = $keyword;
}

// Filtrer par type de produit, catégorie, etc. (garder la logique de filtre existante)
if (!empty($_GET['product_type_id'])) {
    $whereClauses[] = "p.product_type_id = :product_type_id";
    $params[':product_type_id'] = $_GET['product_type_id'];
}

if (!empty($_GET['category_id'])) {
    $whereClauses[] = "p.category_id = :category_id";
    $params[':category_id'] = $_GET['category_id'];
}

if (!empty($_GET['min_price'])) {
    $whereClauses[] = "p.product_price >= :min_price";
    $params[':min_price'] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $whereClauses[] = "p.product_price <= :max_price";
    $params[':max_price'] = $_GET['max_price'];
}

// Construire la clause WHERE
$whereSql = "";
if (!empty($whereClauses)) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// Requête SQL avec les filtres et le mot-clé
$sql = "
    SELECT p.*, c.category_name
    FROM table_product p
    LEFT JOIN table_category c ON p.category_id = c.category_id
    $whereSql
    ORDER BY product_id DESC
    LIMIT :limit OFFSET :offset
";

// Préparer la requête
$stmt = $db->prepare($sql);

// Lier les paramètres
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Pagination
$stmt->bindValue(":limit", $perPage, PDO::PARAM_INT);
$stmt->bindValue(":offset", ($page - 1) * $perPage, PDO::PARAM_INT);
$stmt->execute();

// Récupérer les produits
$recordset = $stmt->fetchAll();

// Compter les résultats pour la pagination
$sqlCount = "
    SELECT COUNT(p.product_id) AS total
    FROM table_product p
    LEFT JOIN table_category c ON p.category_id = c.category_id
    $whereSql
";
$stmtCount = $db->prepare($sqlCount);
foreach ($params as $key => $value) {
    $stmtCount->bindValue($key, $value);
}
$stmtCount->execute();
$row = $stmtCount->fetch();
$total = $row['total'];
$nbPage = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste des Produits</title> <!-- Titre de la page -->
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers le fichier CSS -->
</head>

<body>
    <!-- Lien pour ajouter un produit -->
    <a href="form.php">Ajouter un produit</a>
    <a href="/admin/logout.php">Deconnexion</a>
    <!-- Gestion des filtres -->
    <details class="filter-dropdown">
        <summary>Afficher les filtres</summary>
        <form action="index.php" method="get">
            <label for="product_type_id">Type de produit :</label>
            <select name="product_type_id" id="product_type_id">
                <option value="">-- Tous les types --</option>
                <?php foreach ($types as $type) { ?>
                    <option value="<?= hsc($type['type_id']); ?>" <?= isset($_GET['product_type_id']) && $_GET['product_type_id'] == $type['type_id'] ? 'selected' : ''; ?>>
                        <?= hsc($type['type_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label for="category_id">Catégorie :</label>
            <select name="category_id" id="category_id">
                <option value="">-- Toutes les catégories --</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?= hsc($category['category_id']); ?>" <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                        <?= hsc($category['category_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label for="min_price">Prix min :</label>
            <input type="number" name="min_price" id="min_price" value="<?= hsc($_GET['min_price'] ?? ''); ?>">

            <label for="max_price">Prix max :</label>
            <input type="number" name="max_price" id="max_price" value="<?= hsc($_GET['max_price'] ?? ''); ?>">

            <input type="submit" value="Appliquer les filtres">
        </form>
    </details>
    <form action="index.php" method="get">
        <label for="keyword">Recherche par mot-clé :</label>
        <input type="text" name="keyword" id="keyword" value="<?= hsc($_GET['keyword'] ?? ''); ?>"
            placeholder="Rechercher un produit...">
        <input type="submit" value="Rechercher">
    </form>
    <!-- Début de la table affichant la liste des produits -->
    <table>
        <!-- Légende de la table affichant le nombre total de produits dans la base de données -->
        <caption>Liste des produits : <?php echo $total; ?></caption>

        <!-- En-tête de la table -->
        <thead>
            <tr>
                <th scope="col">Titre</th> <!-- Nom du produit -->
                <th scope="col">Prix</th> <!-- Prix du produit -->
                <th scope="col">Stock</th> <!-- Stock du produit -->
                <th scope="col">Couverture</th>
                <th scope="col">Catégorie</th> <!-- Catégorie -->
                <th scope="col">Action</th> <!-- Actions (modifier/supprimer) -->
            </tr>
        </thead>

        <!-- Corps de la table -->

        <tbody>
            <?php foreach ($recordset as $row) { ?>
                <tr>
                    <!-- Nom du produit -->
                    <td><?= hsc($row['product_name']); ?></td>
                    <!-- Prix du produit -->
                    <td><?= hsc($row['product_price']); ?></td>
                    <!-- Stock du produit -->
                    <td><?= hsc($row['product_stock']); ?></td>
                    <td><img src="http://bdshop/upload/<?= hsc($row['product_image']); ?>" width="75px" height="100px">
                    </td>
                    <!-- Catégorie du produit (ou 'Non spécifiée' si aucune catégorie) -->
                    <td><?= hsc($row['category_name'] ?? 'Non spécifiée'); ?></td>
                    <!-- Actions : Détails, Supprimer, Modifier -->
                    <td>
                        <a href="details.php?id=<?= hsc($row['product_id']) ?>">Détails</a>
                        <a
                            href="delete.php?id=<?= hsc($row['product_id']); ?>&token=<?= $_SESSION['token']; ?>">Supprimer</a>
                        <a href="form.php?id=<?= hsc($row['product_id']); ?>&token=<?= $_SESSION['token']; ?>">Modifier</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php
    // Appel de la fonction pagi
    // Créer une chaîne de requête avec les filtres appliqués
    $queryParams = $_GET;
    unset($queryParams['p']); // Supprimer le paramètre 'p' pour éviter les doublons
    $queryString = http_build_query($queryParams);

    // Passer la chaîne de requête dans l'URL de pagination
    echo pagi($page, $nbPage, 'index.php?' . $queryString);

    ?>
</body>

</html>