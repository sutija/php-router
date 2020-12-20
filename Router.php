<?php

namespace Sutija\Router;

use Closure;
use Exception;

const GET = 'GET';
const PATCH = 'PATCH';
const POST = 'POST';
const PUT = 'PUT';
const DELETE = 'DELETE';
const OPTIONS = 'OPTIONS';
const COPY = 'COPY';

/**
 * @author Marko Šutija <markosutija@gmail.com>
 * @version 1.0
 *
 * Class Router
 *
 */
class Router
{
    protected const CALLBACK_STATIC = 'CALLBACK_STATIC';
    protected const CALLBACK_CLASS = 'CALLBACK_CLASS';
    protected const CALLBACK_CLOSURE = 'CALLBACK_CLOSURE';
    protected static $instance;
    protected $routes;
    /**
     * @var RouterData $routeData
     */
    protected $routeData;

    public function __construct()
    {
        $this->routes = new Routes();

        $this->routeData = new RouterData();
        $this->routeData->setRoute($_SERVER['PATH_INFO']);
        $this->routeData->setRequestMethod($_SERVER['REQUEST_METHOD']);

        if (isset($_SERVER['QUERY_STRING'])) {
            $this->routeData->setRequestQueryParams($_SERVER['QUERY_STRING']);
        }
    }

    public static function getInstance(): Router
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function addRoute(Route $route): void
    {
        $this->routes->add($route);
    }

    public function resolve(string $route = null, string $method = null, string $query = null): string
    {
        $this->fillRouteData($route, $method, $query);

        $requestMethod = ucfirst(strtolower($this->routeData->getRequestMethod()));
        $requestMethodMethod = "resolve$requestMethod";

        $matchedRoute = $this->routes->find($this->routeData->getRoute());

        if (!$matchedRoute) {
            http_response_code(404);
            return false;
        }

        if (!empty($matchedRoute->getAllowedMethods())
            && !in_array($this->routeData->getRequestMethod(), $matchedRoute->getAllowedMethods())) {
            http_response_code(405);
            return false;
        }

        $this->resolveData($matchedRoute->getRoute());
        $this->resolveQueryParams();

        switch ($this->getCallBackType($matchedRoute->getCallback())) {
            case self::CALLBACK_CLOSURE:
                $matchedRoute->getCallback()();
                break;

            case self::CALLBACK_CLASS:
                $this->doClassCallback($matchedRoute);
                break;

            case self::CALLBACK_STATIC:
                $this->doStaticCallback($matchedRoute);
                break;
        };

        return $this->getRouteData()->getRoute();
    }

    protected function fillRouteData(?string $route, ?string $method, ?string $query): void
    {
        if ($route) {
            $this->routeData->setRoute($route);
        }

        if ($query) {
            $this->routeData->setRequestQueryParams($query);
        }

        if ($method) {
            $this->routeData->setRequestMethod($method);
        }
    }

    protected function resolveData(string $routeTemplate)
    {
        $route = explode('/', $this->routeData->getRoute());
        $template = explode('/', $routeTemplate);

        foreach ($template as $key => $value) {
            if (preg_match('/[:]/', $value)) {
                $k = str_replace(':', '', $value);
                if (isset($route[$key])) {
                    $this->routeData->setData($k, $route[$key]);
                }
            }
        }
    }

    protected function resolveQueryParams()
    {
        $queryParams = [];
        $requestQuery = $this->routeData->getRequestQueryParams();

        if ($requestQuery) {
            foreach (explode('&', $requestQuery) as $item) {
                $itemParts = explode('=', $item);
                $queryParams[$itemParts[0]] = $itemParts[1];
            }
        }

        $this->routeData->setQueryParams($queryParams);
    }

    protected function getCallBackType($callback): ?string
    {
        $type = null;

        if (is_string($callback)) {
            $type = self::CALLBACK_STATIC;
        }

        if (is_object($callback) && !($callback instanceof Closure)) {
            $type = self::CALLBACK_CLASS;
        }

        if ($callback instanceof Closure) {
            $type = self::CALLBACK_CLOSURE;
        }

        return $type;
    }

    protected function doClassCallback(Route $matchedRoute)
    {
        $requestMethodMethod = 'resolve' . ucfirst(strtolower($this->routeData->getRequestMethod()));

        // check if we have resolvePost/resolveGet...
        if (method_exists($matchedRoute->getCallback(), $requestMethodMethod)) {
            $matchedRoute->getCallback()->$requestMethodMethod();
        } // Call resolve
        else {
            $matchedRoute->getCallback()->resolve();
        }
    }

    protected function doStaticCallback(Route $matchedRoute)
    {
        $requestMethodMethod = 'resolve' . ucfirst(strtolower($this->routeData->getRequestMethod()));

        if (method_exists($matchedRoute->getCallback(), $requestMethodMethod)) {
            $callableMethod = [$matchedRoute->getCallback(), $requestMethodMethod];
            call_user_func_array($callableMethod, []);
        } elseif (method_exists($matchedRoute->getCallback(), 'resolve')) {
            $callableMethod = [$matchedRoute->getCallback(), 'resolve'];
            call_user_func_array($callableMethod, []);
        } else {
            throw new Exception('Callback not found: ' . $matchedRoute->getCallback());
        }
    }

    public function getRouteData(): RouterData
    {
        return $this->routeData;
    }
}
