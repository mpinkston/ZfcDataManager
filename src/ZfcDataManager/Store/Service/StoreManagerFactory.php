<?php

namespace ZfcDataManager\Store\Service;

use ZfcDataManager\Store\StoreManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StoreManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return StoreManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StoreManager();
    }
}