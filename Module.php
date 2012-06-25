<?php

namespace ZfcDataManager;

use Zend\Loader\StandardAutoloader;

class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfiguration()
    {
        return array(
            'factories' => array(
                'DataManager' => 'ZfcDataManager\Service\DefaultDataManagerFactory',
                'StoreManager' => 'ZfcDataManager\Store\Service\StoreManagerFactory',
                'ModelManager' => 'ZfcDataManager\Model\Service\ModelManagerFactory',
                'FieldManager' => 'ZfcDataManager\Field\Service\FieldManagerFactory',
                'ProxyManager' => 'ZfcDataManager\Proxy\Service\ProxyManagerFactory'
            ),
            'shared' => array(
                'FieldManager' => false // Every model should get its own FieldManager
            )
        );
    }
}
