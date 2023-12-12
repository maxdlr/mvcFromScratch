<?php

use App\Controller\IndexController;
use App\Exception\RouteNotFoundException;
use App\Routing\Route;
use App\Routing\Router;

require_once 'vendor/autoload.php';

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

