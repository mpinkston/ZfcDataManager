<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManager;

interface ProxyInterface
{
    /**
     * @abstract
     * @param DataManager $dataManager
     * @return ProxyInterface
     */
    public function setDataManager(DataManager $dataManager);

    /**
     * @abstract
     * @param $mapping
     * @return mixed
     */
    public function setMapping($mapping);
}