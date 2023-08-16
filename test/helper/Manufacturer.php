<?php

namespace Retort\Test\Helper;

use Retort\Request\RetortRequest;
use Retort\Validation\ValidString;

class Manufacturer extends RetortRequest
{
    #[ValidString(true, 1, 30)]
    public string $name;

    #[ValidString(false, 1, 100)]
    public ?string $address;
}
