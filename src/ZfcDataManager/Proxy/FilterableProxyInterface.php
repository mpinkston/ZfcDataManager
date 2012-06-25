<?php

namespace ZfcDataManager\Proxy;

interface FilterableProxyInterface extends ReadableProxyInterface
{
    /**
     * @abstract
     * @param $filters
     * @return mixed
     */
    public function setFilterBy($filters);
}