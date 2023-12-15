<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\IndexController;
use App\Controller\ProductController;
use App\DependencyInjection\Container;
use App\DependencyInjection\ServiceExistsException;
use App\Exception\RouteNotFoundException;
use App\Repository\ProductRepository;
use App\Routing\Attribute\Route;
use App\Routing\Router;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (php_sapi_name() !== 'cli' && preg_match('/\.(ico|png|jpg|jpeg|css|js|gif)$/', $_SERVER['REQUEST_URI'])) {
    return false;
}

// DATABASE CONNECTION ---------------------------------------------------------------------
$dotenv = new Dotenv();
try {
    $dotenv->loadEnv(__DIR__ . '/../.env');
//    var_dump($dotenv);
} catch (Exception $exception) {
    echo $exception->getCode() . ' // ' . $exception->getMessage();
}

$dbParams = [
    'driver'   => $_ENV['DB_DRIVER'],
    'user'     => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_NAME'],
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
];

$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = $_ENV['APP_ENV'] === 'dev';

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
try {
    $connection = DriverManager::getConnection($dbParams, $config);
} catch (\Doctrine\DBAL\Exception $e) {
    echo $e->getCode() . ' - ' . $e->getMessage();
}

try {
    $entityManager = new EntityManager($connection, $config);
} catch (MissingMappingDriverImplementation $e) {
    echo $e->getCode() . ' - ' . $e->getMessage() . '<br />' . var_dump($e);
}

$productRepository = new ProductRepository($entityManager);

// CREATE USER ---------------------------------------------------------------------

//$user = new User();
//$user
//    ->setEmail("something@something.com")
//    ->setPassword(password_hash("test", PASSWORD_BCRYPT))
//;
//
//$entityManager->persist($user);
//$entityManager->flush();

// TWIG ---------------------------------------------------------------------

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/../var/cache',
    'debug' => $_ENV['APP_ENV'] === 'dev'
]);

// SERVICES ---------------------------------------------------------------------

$container = new Container();

try {
    $container
        ->set(Environment::class, $twig)
        ->set(EntityManager::class, $entityManager)
        ->set(ProductRepository::class, $productRepository);
} catch (ServiceExistsException $e) {
    echo $e->getCode() . ' - ' . $e->getMessage() . '<br />' . var_dump($e);
}

// ROUTER ---------------------------------------------------------------------

$router = new Router($container);
try {
    $router->extractRoutesFromAttributes();
} catch (ReflectionException $e) {
    echo $e->getCode() . ' - ' . $e->getMessage();
}

//  ---------------------------------------------------------------------

if (php_sapi_name() === 'cli') {
    return;
}

[
    'REQUEST_URI'    => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;

try {
    echo $router->execute($uri, $httpMethod);
} catch (RouteNotFoundException) {
    http_response_code(404);
    echo "Page non trouvée";
} catch (Exception $e) {
    http_response_code(500);
    var_dump($e->getMessage());
    echo "Erreur interne, veuillez contacter l'administrateur";
}