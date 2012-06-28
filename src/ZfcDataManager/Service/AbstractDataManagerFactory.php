<?php

namespace ZfcDataManager\Service;

use ZfcDataManager\DataManager;
use ZfcDataManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractDataManagerFactory implements FactoryInterface
{
    /**
     * @abstract
     * @return string
     */
    abstract public function getName();

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @throws InvalidArgumentException
     * @return DataManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (!isset($config['data'][$this->getName()])) {
            throw new InvalidArgumentException(sprintf(
                "No configuration found for data => %s",
                $this->getName()
            ));
        }

        $configuration = $config['data'][$this->getName()];
        $manager = new DataManager($configuration, $serviceLocator);
        return $manager;
    }
}