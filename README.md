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

Using the `Validation` property attributes it is possible to automatically generate Typescript or other types from `RetortRequest` objects for frontend use! (see below)

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

`ValidString`, `ValidNumber`, and `ValidObject` `Validation` objects are provided. Check their [class definitions](Validation) for validation options, e.g., required, min, max...

`ValidObject` can be used for nested objects and arrays of objects.

    class GoingConcern extends RetortRequest
    {
        #[ValidObject(true, Address::class)]
        public Address $address;

        #[ValidObject(true, Employees::class)]
        public array $employees; // use array type
    }

Parameters can be made optional.

    #[ValidString(false, 1)] // $nickname will be a string of length >= 1 and is optional (false)
    public ?string $nickname;

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

## Generating types

Run `vendor/bin/retort_typegen -c <file>` with your config file to generate types. The config file is a [TOML](https://toml.io/en/) file where configuration options and a type definition template are defined. See the sample [config](sample_config.toml) for more details. Templates are defined in [mustache](https://mustache.github.io) format.

Provide the classes to generate. Classes must be autoloaded and an instance of `RetortRequest`.

    # Generate type definitions for these PHP classes
    classes = [
        "Retort\\Test\\Helper\\Jacket"
    ]

Setup the template, in this case a Typescript class template.

    # Sample template for Typescript type generation in mustache format
    template = '''
    interface {{class}} {
        {{#definitions}}
        {{name}}{{#nullable}}?{{/nullable}}: {{type}}{{#iterable}}[]{{/iterable}};
        {{/definitions}}
    }
    '''

Different templates for different languages can be deinfed. For example, here is a flutter class template.

    class {{class}} {
        {{#definitions}}
        final {{type}}{{#nullable}}?{{/nullable}} {{name}};
        {{/definitions}}

        const {{class}}({
            {{#definitions}}
            {{^nullable}}required {{/nullable}}this.{{name}},
            {{/definitions}}
        });

        factory {{class}}.fromJson(Map<String, dynamic> json) {
            return {{class}}(
            {{#definitions}}
            {{name}}: json['{{name}}'],
            {{/definitions}}
            );
        }

        factory {{class}}.fromObject(Map<String, dynamic> json) {
            return {{class}}(
            {{#definitions}}
            {{name}}: json['{{name}}'],
            {{/definitions}}
            );
        }

        Map<String, dynamic> toJson() {
            return {
            {{#definitions}}
            '{{name}}': {{name}},
            {{/definitions}}
            };
        }
    }

PHP types can be mapped to other languages under `typeMap`.

    # Type mappings, e.g., int (php) to number (ts)
    [typeMap]
    int = "number"
