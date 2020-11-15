# PHP Router

Installation:


Public methods:
- `Router::addRoute`
- `Router::routeData`
- `Router::resolve`
- `Router::getRequestMethod`


#### Example of usage:
__Using as callback function:__
```
Router::addRoute('/some/:variable', function () {
    var_dump(Router::routeData);
    echo Router::getMethod();
    // GET
}, [Router::METHOD_GET]);
```


__Using as class:__
```
Router::addRoute('/something/:product_id', new SomeClass());
```
This will do the class call: `$someClass->resolve();`

__Using as static method:__
```
Router::addRoute('/something/:category_id/product/:product_name', SomeClass::class, [
    Router::METHOD_GET
]);
```
This will do the class call: `SomeClass::resolve();`
