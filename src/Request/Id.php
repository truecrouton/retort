<?php

namespace Retort\Request;

use Retort\Request\RetortRequest;
use Retort\Validation\ValidNumber;

class Id extends RetortRequest
{
    #[ValidNumber(true, 1)]
    public int $id;
}
