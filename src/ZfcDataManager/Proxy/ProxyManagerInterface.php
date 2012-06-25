<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManager;

interface ProxyManagerInterface
{
    /**
     * @abstract
     * @param $proxyName
     * @return AbstractProxy
     */
    public function getProxy($proxyName);

    /**
     * @abstract
     * @param $proxies
     * @return mixed
     */
    public function setProxies($proxies);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return ProxyManagerInterface
     */
    public function setDataManager(DataManager $dataManager);
}