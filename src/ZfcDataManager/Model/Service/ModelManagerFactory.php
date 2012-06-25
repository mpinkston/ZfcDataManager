<?php

namespace ZfcDataManager\Model\Service;

use ZfcDataManager\Model\ModelManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModelManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ModelManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ModelManager();
    }
}