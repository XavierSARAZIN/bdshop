<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/function.php'; // Contient des fonctions utilitaires
// Inclusion du fichier 'protect.php' situé dans le répertoire '/admin/include/'
// Le chemin absolu vers ce fichier est obtenu en utilisant '$_SERVER['DOCUMENT_ROOT']'
// Cela garantit que le fichier est inclus de manière correcte, peu importe l'endroit où le script est exécuté.

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/include/protect.php';

// À partir d'ici, vous pouvez inclure des fonctionnalités supplémentaires,
// sachant que le fichier 'protect.php' a été inclus et exécuté avec les protections qu'il contient.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <a class="btn btn-primary" href="./login.php">Accéder à la page de login</a>
</body>

</html>