# Retort

Easy PHP route definition and request validation using attributes introduced in PHP 8! A lot of routing boilerplate is eliminated and request validation is extremely straightforward!

Just add `Route` attributes your controller class methods to define route methods. This removes the need for repetitive boilerplate route definition files.

    #[Route('GET', '/thing/{id:\d+}')]
    public function thingGet(ThingGetRequest $request): array {}

To validate requests, add `Validation` attributes to request class properties and use `createObject()` to validate and create request objects. Make sure the route methods have the desired request parameters.

    use Retort\Validation\ValidNumber;
    use Retort\Validation\ValidString;

    class ThingGetRequest extends RetortRequest
    {
        #[ValidNumber(true, 1)] // $id will be a number >= 1 and is required (true)
        public int $id;
    }

    $request = Validation::createObject(ThingGetRequest::class, ['id' => 0]); // throws Error
    $request = Validation::createObject(ThingGetRequest::class, ['id' => 1]); // creates a ThingGetRequest object with id = 1

Using reflection and the `Validation` property attributes it will be possible to automatically generate Typescript or other types from `RetortRequest` objects for frontend use! (Working on this feature...)

## Define and call a route method

Use the Route attribute to define route methods within your controller class.

    use Retort\Mapping\Attributes\Route;

    #[Route('GET', '/thing/{id:\d+}')]
    public function thingGet(Id $request): array
    {
        // return a "thing"
        return ['name' => 'A thing'];
    }

Use the `getRoutes()` helper to get the routes for each controller class.

    use Retort\Mapping\Helper;

    $routes = Helper::getRoutes(YourController::class, AnotherController::class);

Use the route information in your router. Here is an example using [FastRoute](https://github.com/nikic/FastRoute), but it should work with many different routers.

    $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
        $routes = Helper::getRoutes(YourController::class);

        foreach ($routes as $route) {
            $r->addRoute($route['requestMethod'], $route['uri'], [
                'class' => $route['class'],
                'classMethod' => $route['classMethod'],
                'requestType' => $route['requestType']
            ]);
        }
    });

Use the route information to call a route method.

    // Validate the request (see below)
    // $route['requestType'] specifies the request class
    $request = Validation::createObject($route['requestType'], $payload);

    $class = new $route['class'](); // $route['class'] specifies the controller class
    $method = $route['classMethod']; // $route['classMethod] specifies the class method

    header('Content-Type: application/json; charset=utf-8');
    print json_encode($class->$method($request)); // call the class method

## Validate a request

Use `Validation::createObject()` to validate requests. `Validation::createObject()` will create the specified object or throw an `Error` if validation fails.

    class ThingGetRequest extends RetortRequest
    {
        #[ValidNumber(true, 1)] // $id will be a number >= 1 and is required (true)
        public int $id;
    }

    $request = Validation::createObject(ThingGetRequest::class, ['id' => 0]); // throws Error
    $request = Validation::createObject(ThingGetRequest::class, ['id' => 1]); // creates a ThingGetRequest object with id = 1

## Dependency injection

A few `DependencyController`s are provided as examples. For instance, the `MysqlController` constructor accepts a `mysqli` object `$myDb` which can then be used within the controller such as through the included `executeQuery()` method.

    use Retort\Controller\MysqlController;

    class ThingController extends MysqlController
    {
        #[Route('GET', '/thing/post')]
        public function thingPost(ThingPostRequest $request): array
        {
            $this->executeQuery('insert into things (name) values(?)', [$request->name]);
            return ['thingId' => $this->myDb->insert_id];
        }
    }

    $db = new mysqli('localhost', 'user', 'super_secure_password', 'database');
    $class = new ThingController($db); // inject $db
