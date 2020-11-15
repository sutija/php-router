<?php

/**
 * @author Marko Šutija <markosutija@gmail.com>
 * @version 1.0
 *
 * Class Router
 *
 */
class Router
{
    const METHOD_GET = 'GET';
    const METHOD_PATCH = 'PATCH';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_COPY = 'COPY';

    protected static $routes = [];

    /**
     * @var \stdClass $queryParams
     */
    protected static $queryParams;

    /**
     * @var RouterData $routeData
     */
    protected static $routeData;

    static function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    static function addRoute($route, $callback, array $allowedRequestMethods = [])
    {
        static::$routes[$route] = [
            'methods' => $allowedRequestMethods,
            'callback' => $callback
        ];
    }

    static function resolve($route, $method)
    {
        $matchedRoute = static::matchRoute($route);

        if (!$matchedRoute) {
            return false;
        }

        $url = explode('?', $route);
        $callback = static::$routes[$matchedRoute]['callback'];

        if (is_string($callback)) {
            $callableMethod = [$callback, strtolower($method)];
            if (method_exists($callback, strtolower($method))) {
                call_user_func_array($callableMethod, []);
            } else {
                echo 'FORBIDDEN';
            }
        } else if (is_object($callback)){
            $callback->resolve();
        } else if (is_callable($callback)) {
            $callback('nesto');
        }

        return $url[0];
    }

    static function matchRoute($route)
    {
        // Separate current route
        $separatedRoute = array_values(array_filter(explode('/', $route), function ($item) {
            return $item !== '';
        }));

        // Filter routes to find exact route
        $matchedRoutes = array_values(array_filter(array_keys(static::$routes), function ($routeTemplate) use ($separatedRoute, $route) {
            $variableIndex = [];

            // First separate route
            $routeParts = array_values(array_filter(explode('/', $routeTemplate), function ($item) {
                return $item !== '';
            }));

            // Then locate all indexes of route variables
            foreach ($routeParts as $key => $part) {
                if ($part[0] === ':') {
                    // Add variable index
                    $variableIndex[] = $key;
                }
            }

            // Replace variable indexes in both routes
            foreach ($variableIndex as $index) {
                if (isset($separatedRoute[$index])) {
                    $separatedRoute[$index] = '__VARIABLE__';
                }
                if (isset($routeParts[$index])) {
                    $routeParts[$index] = '__VARIABLE__';
                }
            }

            // Compare routes
            if (implode('/', $separatedRoute) === implode('/', $routeParts)) {
                static::$routeData = static::resolveData($routeTemplate, $route);
                return true;
            }

            return false;
        }));


        return !empty($matchedRoutes) ? $matchedRoutes[0] : false;
    }

    static function getQueryParams()
    {
        $queryParams = [];

        if (isset($_SERVER['QUERY_STRING'])) {
            foreach (explode('&', $_SERVER['QUERY_STRING']) as $item) {
                $itemParts = explode('=', $item);
                $queryParams[$itemParts[0]] = $itemParts[1];
            }
        }

        return $queryParams;
    }

    /**
     * @param $routeTemplate
     * @param $route
     * @return RouterData
     */
    public static function resolveData($routeTemplate, $route)
    {
        $routerData = new RouterData();

        $route = explode('/', $route);
        $template = explode('/', $routeTemplate);

        foreach ($template as $key => $value) {
            if (preg_match('/[:]/', $value)) {
                $k = str_replace(':', '', $value);
                if (isset($route[$key])) {
                    $routerData->setData($k, $route[$key]);
                }
            }
        }

        return $routerData;
    }
}
