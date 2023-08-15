<?php

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Retort\Mapping\Helper;
use Retort\Request\Id;
use Retort\Test\Helper\TestController;

class RouteTest extends TestCase
{
    public function testRoute(): array
    {
        $routes = Helper::getRoutes(TestController::class);

        $this->assertIsArray($routes);
        $this->assertCount(1, $routes);

        $route = $routes[0];
        $this->assertEquals(TestController::class, $route['class']);
        $this->assertEquals('testGet', $route['classMethod']);
        $this->assertEquals('GET', $route['requestMethod']);
        $this->assertEquals(Id::class, $route['requestType']);
        $this->assertEquals('/test', $route['uri']);

        return $route;
    }

    #[Depends('testRoute')]
    public function testRouteMethod($route)
    {
        $controller = new $route['class']();
        $method = $route['classMethod'];

        $req = new Id();
        $req->id = 1;

        $res = $controller->$method($req);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('id', $res);
        $this->assertEquals($req->id, $res['id']);
    }
}
