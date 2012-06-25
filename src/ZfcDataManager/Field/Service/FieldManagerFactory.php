<?php

namespace ZfcDataManager\Field\Service;

use ZfcDataManager\Field\FieldManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FieldManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FieldManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FieldManager();
    }
}