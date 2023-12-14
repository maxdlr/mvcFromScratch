<?php

namespace App\Routing;

use App\Exception\RouteNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function __construct(
        private ContainerInterface $container,
    )
    {
    }
    public function addRoute(Route $route): self
    {
        $this->routes[] = $route;
        return $this;
    }

    public function getRoute(string $uri, string $httpMethod): ?Route
    {
        foreach ($this->routes as $savedRoute) {

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
    public function getMethodParams(string $method): array
    {

        $methodInfos = new ReflectionMethod($method);
        $methodParameters = $methodInfos->getParameters();

        $params = [];
        foreach ($methodParameters as $param) {
            $paramType = $param->getType();
            $paramTypeFQCN = $paramType->getName();
            $params[] = $this->container->get($paramTypeFQCN);
        }

        return $params;
    }
}