<?php

require_once 'vendor/autoload.php';

use App\Controller\IndexController;
use App\Exception\RouteNotFoundException;
use App\Routing\Route;
use App\Routing\Router;

$dbConfig = parse_ini_file(__DIR__ . '/../config/db.ini', true);

if ($dbConfig === false) {
    echo "Fichier de config de la db manquant. Créez db.ini dans config/";
    exit;
}

[
    'DB_HOST' => $host,
    'DB_PORT' => $port,
    'DB_NAME' => $dbName,
    'DB_CHARSET' => $charset,
    'DB_USERNAME' => $user,
    'DB_PASSWORD' => $password
] = $dbConfig;

try {
    $dsn = "mysql:host=$host; port=$port;dbname=$dbName;charset=$charset";
    $pdo = new PDO($dsn, $user, $password);

} catch (PDOException) {
    echo "Erreur interne lors de la connexion a la base de donnée";
    exit;
}


$router = new Router();

$router
    ->addRoute(new Route(
        '/',
        'app_home',
        'GET',
        IndexController::class,
        'home'
    ))
    ->addRoute(new Route(
        '/contact',
        'app_contact',
        'GET',
        IndexController::class,
        'contact'
    ));



[
    'REQUEST_URI' => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;

try {
    echo $router->execute($uri, $httpMethod);
} catch (RouteNotFoundException $e) {
    http_response_code(404);
    echo 'Not Found';
}

