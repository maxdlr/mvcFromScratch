<?php

namespace App\Routing;

class Route
{
    public function __construct(
        private string $uri,
        private string $name,
        private string $httpMethod,
        private string $controllerClass,
        private string $controllerMethod
    )
    {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getControllerMethod(): string
    {
        return $this->controllerMethod;
    }
}