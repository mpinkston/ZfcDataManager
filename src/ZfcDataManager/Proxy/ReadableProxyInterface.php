<?php

namespace ZfcDataManager\Proxy;

interface ReadableProxyInterface extends ProxyInterface
{
    /**
     * @abstract
     * @param $id
     * @return mixed
     */
    public function read($id);

    /**
     * @abstract
     * @param int $startIndex
     * @param null $pageSize
     * @return mixed
     */
    public function fetch($startIndex = 0, $pageSize = null);

    /**
     * @abstract
     * @return mixed
     */
    public function getTotalCount();
}