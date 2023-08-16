<?php

namespace Retort\Validation;

use Error;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Retort\Request\RetortRequest;

class Validation
{
    public bool $notNullable = false;
    public string $propertyName;

    public static function createObject(?string $class, $object)
    {
        if (empty($class)) return null;
        if (!is_subclass_of($class, RetortRequest::class)) throw new Error('Unrecognized object class.');

        $reflection = new ReflectionClass($class);
        $props = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $created = new $class();

        foreach ($props as $prop) {
            $propName = $prop->name;

            $attrs = $prop->getAttributes(Validation::class, ReflectionAttribute::IS_INSTANCEOF);
            if (empty($attrs)) continue;

            /* @var Base $instance */
            $instance = $attrs[0]->newInstance();
            $instance->propertyName = $propName;
            $value = $object[$propName] ?? null;

            if (!$instance->notNullable && $value == null) {
                $created->$propName = null;
                continue;
            }

            $propType = $prop->getType()?->getName();

            if ($propType == 'array') {
                if (!is_array($value)) throw new Error("$propName is not an array.");
                foreach ($value as $v) {
                    $created->$propName[] = $instance->validate($v);
                }
            } else {
                $created->$propName = $instance->validate($value);
            }
        }

        return $created;
    }

    public function validate($value)
    {
        throw new Error('Invalid value.');
    }

    public function throwError(string $message)
    {
        throw new Error($message);
    }
}
