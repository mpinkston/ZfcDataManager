<?php

namespace ZfcDataManager\Store;

use ZfcDataManager\DataManagerAwareInterface;

interface StoreManagerInterface extends DataManagerAwareInterface
{
    /**
     * @abstract
     * @param $stores
     * @return mixed
     */
    public function setStores($stores);

    /**
     * @abstract
     * @param $config
     * @return StoreInterface
     */
    public function createStore($config);

    /**
     * @abstract
     * @param $storeName
     * @return StoreInterface
     */
    public function getStore($storeName);
}