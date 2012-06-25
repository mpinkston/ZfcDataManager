<?php

namespace ZfcDataManager\Proxy\Service;

use ZfcDataManager\Proxy\ProxyManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProxyManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ProxyManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ProxyManager();
    }
}