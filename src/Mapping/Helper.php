<?php

namespace Retort\Mapping;

use ReflectionClass;
use ReflectionMethod;
use Retort\Mapping\Attributes\Route;

class Helper
{
    public static function getRoutes(string ...$controllerClasses): array
    {
        $routes = [];

        foreach ($controllerClasses as $class) {
            $reflection = new ReflectionClass($class);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $attrs = $method->getAttributes(Route::class);
                if (empty($attrs)) continue;

                $params = $method->getParameters();
                if (empty($params)) $requestType = null;
                else $requestType = $params[0]?->getType()?->getName();

                $instance = $attrs[0]->newInstance();

                $routes[] = [
                    'class' => $class,
                    'classMethod' => $method->name,
                    'requestMethod' => $instance->method,
                    'requestType' => $requestType,
                    'uri' => $instance->uri
                ];
            }
        }

        return $routes;
    }
}
