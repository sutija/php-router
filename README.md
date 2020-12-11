# PHP Router

Minimum requirement PHP 7.1

### Installation:

`composer --save sutija\php-router`

### Usage:

__Using as callback function:__

```php
<?php
$router = \Sutija\Router\Router::getInstance();

$routeArticles = new \Sutija\Router\Route();

$routeArticles
->setRoute('/articles/:alias')
->setAllowedMethods(['GET'])
->setCallback(function() use ($router) {
    // Get alias from route
    $alias = $router->getRouteData()->getData('alias');
    // Do something...
});

$router->addRoute($routeArticles);

$router->resolve();
```

__Using as class:__

```php
<?php
class ArticlesController {
    public function resolve() {
        $alias = \Sutija\Router\Router::getInstance()->getRouteData()->getData('alias');
        // Do something...
    }
    
    public function resolveGet() {
        $alias = \Sutija\Router\Router::getInstance()->getRouteData()->getData('alias');
        $otherParam = \Sutija\Router\Router::getInstance()->getRouteData()->getData('other_param');
        // Do something...
    }
}

$router = \Sutija\Router\Router::getInstance();

$routeArticles = new \Sutija\Router\Route();

$routeArticles
->setRoute('/articles/:alias/something/:other_param')
->setAllowedMethods(['GET', 'POST'])
->setCallback(new ArticlesController());

$router->addRoute($routeArticles);

$router->resolve();
```

In the above example when we do the request with __GET__ method router will try to find `resolveGet` method in ArticleController and try to execute it. In case that's not found it will try to execute `resolve` method.

__Using as static method:__


```php
<?php
class ArticlesController {
    public static function resolveGet() {
        $alias = \Sutija\Router\Router::getInstance()
        ->getRouteData()
        ->getData('alias');
        // Do something...
    }
    
    public static function resolvePost() {
        $alias = \Sutija\Router\Router::getInstance()
        ->getRouteData()
        ->getData('alias');
        // Do something...
    }
}

$router = \Sutija\Router\Router::getInstance();

$routeArticles = new \Sutija\Router\Route();

$routeArticles
->setRoute('/articles/:alias')
->setAllowedMethods(['GET', 'POST'])
->setCallback(ArticlesController::class);

$router->addRoute($routeArticles);

$router->resolve();
```

In the above example when we do the request with __GET__ method router will try to find static `resolveGet` method in ArticleController and try to execute it. In case that's not found it will try to execute static `resolve` method.

To access query params use:
```php
// GET /articles/rainbow/?colors=10&height=20
$queryParams = \Sutija\Router\Router::getInstance()->getRouteData()->getQueryParams();
// [color => 10, height => 20]

echo \Sutija\Router\Router::getInstance()->getRouteData()->getQueryParam('colors');
// 10
```

