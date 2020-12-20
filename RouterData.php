<?php

namespace Sutija\Router;

class RouterData
{
    protected $data = [];
    protected $requestQueryParams;
    protected $queryParams;
    protected $route;
    protected $requestMethod;

    public function getRequestQueryParams(): ?string
    {
        return $this->requestQueryParams;
    }

    public function setRequestQueryParams(string $requestQueryParams)
    {
        $this->requestQueryParams = $requestQueryParams;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route)
    {
        $this->route = $route;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function setRequestMethod(string $requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    public function getData(string $key): string
    {
        return $this->data[$key];
    }

    public function setData(string $key, string $data)
    {
        $this->data[$key] = $data;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function setQueryParams(array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    public function getQueryParam(string $key): ?string
    {
        if (isset($this->queryParams[$key])) {
            return $this->queryParams[$key];
        }

        return null;
    }
}
