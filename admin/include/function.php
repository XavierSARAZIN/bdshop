<?php
function hsc($value)
{
    // Au moment ou on veut afficher quelque chose, vérifie si la valeur passée est nulle
    if (is_null($value)) {
        return ""; // Si la valeur est nulle, on retourne une chaîne vide
    } else {
        // Si la valeur n'est pas nulle, on applique htmlspecialchars pour échapper les caractères spéciaux
        return htmlspecialchars($value);
    }
}
function redirect($url)
{
    header("location:" . $url);
    exit();
}



function pagi($currentPage, $totalPages, $urlBase = 'index.php', $range = 8)
{
    // Vérifie que les valeurs sont valides
    if ($totalPages <= 1) {
        return ''; // Pas de pagination si une seule page
    }

    // Récupère les paramètres de la requête actuelle (filtres)
    $queryParams = $_GET;

    // Supprime le paramètre de la page pour ne pas dupliquer avec $p
    unset($queryParams['p']);

    // Si le mot-clé est déjà présent dans l'URL, on ne l'ajoute pas à nouveau
    // Vérifie si le mot-clé est présent dans $_GET
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        unset($queryParams['keyword']); // Enlève 'keyword' du tableau des paramètres
    }

    // Convertit les paramètres de la requête en chaîne de caractères pour l'ajouter à l'URL
    $queryString = http_build_query($queryParams);

    // Début du HTML de la pagination
    $html = '<nav class="pagination"><ul>';

    // Lien vers la première page
    if ($currentPage > 1) {
        // Vérifie si l'URL de base contient déjà un ?
        $html .= '<li><a href="' . $urlBase . (strpos($urlBase, '?') === false ? '?' : '&') . $queryString . '&p=1">← ←</a></li>';
        $html .= '<li><a href="' . $urlBase . (strpos($urlBase, '?') === false ? '?' : '&') . $queryString . '&p=' . ($currentPage - 1) . '">←</a></li>';
    }

    // Détermine les pages à afficher
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);

    // Ajoute "..." avant si nécessaire
    if ($start > 1) {
        $html .= '<li><span>. . .</span></li>';
    }

    // Génère les liens des pages visibles
    for ($i = $start; $i <= $end; $i++) {
        $class = ($i == $currentPage) ? 'class="current"' : '';
        $html .= '<li ' . $class . '><a href="' . $urlBase . (strpos($urlBase, '?') === false ? '?' : '&') . $queryString . '&p=' . $i . '">' . $i . '</a></li>';
    }

    // Ajoute "..." après si nécessaire
    if ($end < $totalPages) {
        $html .= '<li><span>. . .</span></li>';
    }

    // Lien vers la dernière page
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . $urlBase . (strpos($urlBase, '?') === false ? '?' : '&') . $queryString . '&p=' . ($currentPage + 1) . '">→</a></li>';
        $html .= '<li><a href="' . $urlBase . (strpos($urlBase, '?') === false ? '?' : '&') . $queryString . '&p=' . $totalPages . '">→ →</a></li>';
    }

    // Si le mot-clé existe, on l'ajoute correctement à l'URL
    if (isset($keyword)) {
        // Vérifie si queryString est vide ou non
        if (!empty($queryString)) {
            $queryString .= '&keyword=' . urlencode($keyword); // Ajoute avec & si des paramètres existent déjà
        } else {
            $queryString .= 'keyword=' . urlencode($keyword); // Ajoute avec ? si aucun paramètre n'existe
        }
    }

    // Fin du HTML de la pagination
    $html .= '</ul></nav>';

    return $html;
}

?>