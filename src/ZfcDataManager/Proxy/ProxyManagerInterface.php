<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManagerAwareInterface;

interface ProxyManagerInterface extends DataManagerAwareInterface
{
    /**
     * @abstract
     * @param $proxies
     * @return mixed
     */
    public function setProxies($proxies);

    /**
     * @abstract
     * @param $proxyName
     * @return ProxyInterface
     */
    public function getProxy($proxyName);
}