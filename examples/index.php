<?php
require_once '../vendor/autoload.php';

use Sutija\Router\Route;
use Sutija\Router\Router;
use const Sutija\Router\GET;
use const Sutija\Router\POST;


class M
{
    public static function resolve()
    {
        var_dump(Router::getInstance()->getRouteData());
    }

    public static function resolveGet()
    {
        echo 'GET';
        var_dump(Router::getInstance()->getRouteData());
    }
}


$router = Router::getInstance();

$router->addRoute((new Route())
    ->setRoute('/pages/:page_id/')
    ->setAllowedMethods([POST, GET])
    ->setCallback(M::class)
);

$router->addRoute((new Route())
    ->setRoute('/pages/:page_id/test/:x')
    ->setAllowedMethods([POST, GET])
    ->setCallback(function () {
        M::resolveGet();
    })
);

$router->resolve();

