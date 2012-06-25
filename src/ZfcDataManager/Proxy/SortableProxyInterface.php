<?php

namespace ZfcDataManager\Proxy;

interface SortableProxyInterface extends ReadableProxyInterface
{
    /**
     * @abstract
     * @param $sorters
     * @return mixed
     */
    public function setSortBy($sorters);
}