<?php
require_once './RouterData.php';
require_once './Router.php';
require_once './SomeClass.php';

Router::addRoute('/some/:variable', function () {

}, [Router::METHOD_GET]);

Router::addRoute('/something/:product_id', new SomeClass());

Router::addRoute('/something/:category_id/product/:product_name', SomeClass::class, [
    Router::METHOD_GET
]);

echo Router::resolve('/something/124', Router::METHOD_GET);
