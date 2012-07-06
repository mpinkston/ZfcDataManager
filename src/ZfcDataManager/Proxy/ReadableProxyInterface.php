<?php

namespace ZfcDataManager\Proxy;

use \Traversable;
use ZfcDataManager\Model\ModelInterface;

interface ReadableProxyInterface extends ProxyInterface
{
    /**
     * @abstract
     * @param $id
     * @return ModelInterface
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