<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\Model\ModelInterface;
use ZfcDataManager\DataManagerAwareInterface;

interface ProxyInterface extends DataManagerAwareInterface
{
    /**
     * @abstract
     * @param $options
     * @return mixed
     */
    public function setFromArray($options);

    /**
     * @abstract
     * @param $model
     * @return mixed
     */
    public function setModel($model);

    /**
     * @abstract
     * @param $mapping
     * @return mixed
     */
    public function setMapping($mapping);
}