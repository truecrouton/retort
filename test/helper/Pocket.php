<?php

namespace Retort\Test\Helper;

use Retort\Request\RetortRequest;
use Retort\Validation\ValidNumber;
use Retort\Validation\ValidString;

class Pocket extends RetortRequest
{
    #[ValidString(true, 1, 30)]
    public string $location;

    #[ValidNumber(true, 0)]
    public int $hasZipper;

    #[ValidString(false, 1, 50)]
    public ?array $contents;
}
