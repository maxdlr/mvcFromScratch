<?php

namespace App\Routing;

use App\Exception\RouteNotFoundException;
use Twig\Environment;

class Router
{
    public function __construct(private Environment $twig)
    {
    }

    /**
     * @var Route[]
     */
    private array $routes = [];

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
     */
    public function execute(
        string $uri,
        string $httpMethod
    ): string
    {
        $route = $this->getRoute($uri, $httpMethod);
        if ($route === null)
            throw new RouteNotFoundException("La page n'existe pas");

        $controllerClass = $route->getControllerClass();
        $controller = $route->getControllerMethod();
        $controllerInstance = new $controllerClass($this->twig);

        return $controllerInstance->$controller();
    }
}