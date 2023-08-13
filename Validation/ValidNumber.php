<?php

namespace Retort\Validation;

use Attribute;
use Error;

#[Attribute]
class ValidNumber extends Validation
{
    public function __construct(
        bool $required,
        public int $min,
        public ?int $max = null,
        public ?string $regEx = null,
    ) {
        $this->notNullable = $required;
    }

    public function validate($value)
    {
        if (is_numeric($value) && $value >= $this->min) {
            return $value;
        } else {
            throw new Error("'$value' is an invalid number for " . $this->propertyName . '.');
        }
    }
}
