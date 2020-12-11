<?php


namespace Sutija\Router;


class Routes
{
    protected $routes = [];

    public function add(Route $route) {
        $this->routes[$route->getRoute()] = $route;
    }

    public function find(string $route): ?Route {
        // Separate current route
        $separatedRoute = array_values(array_filter(explode('/', $route), function ($item) {
            return $item !== '';
        }));

        // Filter routes to find exact route
        $matchedRoutes = array_values(array_filter(array_keys($this->routes),
            function ($routeTemplate) use ($separatedRoute, $route) {
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
                    return true;
                }

                return false;
            }));


        return !empty($matchedRoutes) ? $this->routes[$matchedRoutes[0]] : null;
    }
}
