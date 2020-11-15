<?php


class RouterData
{
    protected $data = [];
    protected $queryParams;

    /**
     * @param string $key
     * @return mixed
     */
    public function getData($key)
    {
        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function setData($key, $data)
    {
        $this->data[$key] = $data;
    }

    /**
     * @return mixed
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param mixed $queryParams
     */
    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;
    }
}
