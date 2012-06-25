<?php

namespace ZfcDataManager\Store;

use ZfcDataManager\DataManager;
use ZfcDataManager\Store\StoreManager;

interface StoreManagerInterface
{
    /**
     * @abstract
     * @param $storeName
     * @return mixed
     */
    public function getStore($storeName);

    /**
     * @abstract
     * @param $stores
     * @return mixed
     */
    public function setStores($stores);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return StoreManagerInterface
     */
    public function setDataManager(DataManager $dataManager);
}