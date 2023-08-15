<?php

namespace Retort\Test\Helper;

use Retort\Controller\RetortController;
use Retort\Mapping\Attributes\Route;
use Retort\Request\Id;

class TestController extends RetortController
{
    #[Route('GET', '/test')]
    public function testGet(Id $request): array
    {
        return ['id' => $request->id];
    }
}
