<?php

namespace App\Routing\Attribute;

use Attribute;

#[Attribute]
class Route
{
    public function __construct(
        private string $uri,
        private string $name,
        private string $httpMethod,
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
}