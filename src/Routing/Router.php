<?php

namespace App\Routing;

use App\Exception\RouteNotFoundException;
use App\Routing\Attribute\AttributeManager;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

readonly class Router
{
    private AttributeManager $attributeManager;
    public function __construct(
        private ContainerInterface $container,
    )
    {
        $this->attributeManager = new AttributeManager();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function extractRoutesFromAttributes(): array
    {
        $controllerNames = $this->attributeManager->getPhpFileNamesFromDir(
            __DIR__ . '/../Controller',
            ['AbstractController.php']
        );

        $routes = [];
        foreach ($controllerNames as $controller) {
            $controllerInfo = new ReflectionClass("App\Controller\\" . $controller);
            $routedMethods = $controllerInfo->getMethods();

            foreach ($routedMethods as $routedMethod) {
                if ($routedMethod->getAttributes()) {
                    $route = $routedMethod
                        ->getAttributes('App\Routing\Attribute\Route')[0]
                        ->newInstance();

                    $route = new Route(
                        $route->getUri(),
                        $route->getName(),
                        $route->getHttpMethod(),
                        "App\Controller\\" . $routedMethod->getDeclaringClass()->getShortName(),
                        $routedMethod->getName(),
                    );

                    $routes[] = $route;
                }
            }
        }
        return $routes;
    }

    /**
     * @throws ReflectionException
     */
    private function getRoute(string $uri, string $httpMethod): ?Route
    {
        foreach ($this->extractRoutesFromAttributes() as $savedRoute) {

            if ($savedRoute->getUri() === $uri && $savedRoute->getHttpMethod() === $httpMethod) {
                return $savedRoute;
            }
        }
        return null;
    }

    /**
     * @throws RouteNotFoundException
     * @throws ReflectionException
     */
    public function execute(
        string $uri,
        string $httpMethod
    ): string
    {
        $route = $this->getRoute($uri, $httpMethod);
        if ($route === null)
            throw new RouteNotFoundException("La page n'existe pas");

        // Constructor
        $controllerClass = $route->getControllerClass();
        $constructorParams = $this->getMethodParams($controllerClass . '::__construct');
        $controllerInstance = new $controllerClass(...$constructorParams);

        // Method
        $method = $route->getControllerMethod();
        $methodParams = $this->getMethodParams($controllerClass . '::' . $method);

        return $controllerInstance->$method(...$methodParams);
    }

    /**
     * @throws ReflectionException
     */
    private function getMethodParams(string $method): array
    {
        $methodInfos = new ReflectionMethod($method);
        $methodParameters = $methodInfos->getParameters();

        $params = [];
        foreach ($methodParameters as $param) {
            $paramType = $param->getType();
            $paramTypeFQCN = $paramType->getName();
            try {
                $params[] = $this->container->get($paramTypeFQCN);
            } catch (ContainerExceptionInterface $e) {
                var_dump($e);
            }
        }

        return $params;
    }
}