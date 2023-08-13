<?php

namespace Retort\Mapping\Attributes;

use Attribute;

#[Attribute]
class Route
{
    public function __construct(
        public string $method,
        public string $uri
    ) {
    }
}
