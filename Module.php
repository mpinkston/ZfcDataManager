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
            ),
            'invokables' => array(
                'ModelManager' => 'ZfcDataManager\Model\ModelManager',
                'ProxyManager' => 'ZfcDataManager\Proxy\ProxyManager',
                'StoreManager' => 'ZfcDataManager\Store\StoreManager',
                'FieldManager' => 'ZfcDataManager\Field\FieldManager'
            ),
            'shared' => array(
                'FieldManager' => false
            )
        );
    }
}
