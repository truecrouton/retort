<?php

namespace Retort\Validation;

use Attribute;

#[Attribute]
class ValidObject extends Validation
{
    public function __construct(
        bool $required,
        public string $class,
    ) {
        $this->notNullable = $required;
    }

    public function validate($object)
    {
        return self::createObject($this->class, $object);
    }
}
