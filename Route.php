<?php


namespace Sutija\Router;


class Route
{
    protected $route;
    protected $allowedMethods = [];
    protected $callback;

    public static function get() {
        return new self();
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    public function setAllowedMethods($allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }
}
