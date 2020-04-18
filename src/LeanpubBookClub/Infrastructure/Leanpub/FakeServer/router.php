<?php
declare(strict_types=1);

error_reporting(E_ALL);

if ($_SERVER['REQUEST_URI'] === '/title_page.jpg') {
    header('Content-Type: image/jpg');
    echo file_get_contents(__DIR__ . '/title_page.jpg');
    exit;
}

$bookSlug = $_ENV['LEANPUB_BOOK_SLUG'];

if (!isset($_GET['api_key']) || $_GET['api_key'] !== $_ENV['LEANPUB_API_KEY']) {
    header('Location: https://leanpub.com/authors', true, 302);
    exit;
}

$pathInfo = $_SERVER['REQUEST_URI'];
$requestUriParts = explode('?', $pathInfo);
$pathInfo = (string)reset($requestUriParts);

if (preg_match('#^/' . preg_quote($bookSlug) . '\.json$#', $pathInfo) > 0) {
    header('Content-Type: application/json');
    echo file_get_contents(__DIR__ . '/book-summary.json');
    exit;
} elseif (preg_match('#^/' . preg_quote($bookSlug) . '/individual_purchases\.json$#', $pathInfo) > 0) {
    header('Content-Type: application/json');
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $jsonFile = __DIR__ . '/individual-purchases-page-' . $page . '.json';
    if (!file_exists($jsonFile)) {
        echo file_get_contents(__DIR__ . '/no-more-individual-purchases.json');
    } else {
        echo file_get_contents($jsonFile);
    }
    exit;
}

header("HTTP/1.0 404 Not Found");
echo 'Page not found';
