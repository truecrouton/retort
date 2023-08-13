<?php

namespace Retort\Validation;

use Attribute;
use Error;

#[Attribute]
class ValidString extends Validation
{
    public function __construct(
        bool $required,
        public int $minLength,
        public int $maxLength,
        public ?string $regEx = null
    ) {
        $this->notNullable = $required;
    }

    public function validate($value): string
    {
        if (is_string($value) && (mb_strlen($value) >= $this->minLength)) {
            return $value;
        } else {
            throw new Error("'$value' is an invalid value for " . $this->propertyName . '.');
        }
    }
}
