<?php

namespace Retort\Test\Helper;

use Retort\Request\RetortRequest;
use Retort\Validation\ValidNumber;
use Retort\Validation\ValidObject;
use Retort\Validation\ValidString;

class Jacket extends RetortRequest
{
    #[ValidString(true, 1, 100)]
    public string $description;

    #[ValidNumber(true, 10)]
    public int $price;

    #[ValidObject(false, Manufacturer::class)]
    public ?Manufacturer $manufacturer;

    #[ValidObject(true, Pocket::class)]
    public array $pockets;
}
