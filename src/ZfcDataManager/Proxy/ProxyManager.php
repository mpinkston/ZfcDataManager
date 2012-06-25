<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManager;

class ProxyManager implements ProxyManagerInterface
{
    /**
     * @var array
     */
    public $proxies;

    /**
     * @var array
     */
    public $loadedProxies;

    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @param $proxyName
     * @return AbstractProxy
     */
    public function getProxy($proxyName)
    {
        return $this->loadProxy($proxyName);
    }

    /**
     * @param $proxyName
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function loadProxy($proxyName)
    {
        if (isset($this->loadedProxies[$proxyName])) {
            return $this->loadedProxies[$proxyName];
        }

        if (isset($this->proxies[$proxyName])) {
            $proxy = $this->createProxyFromArray($this->proxies[$proxyName]);
            $this->loadedProxies[$proxyName] = $proxy;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                "No instance or configuration could be found for the specified proxy (%s)",
                $proxyName
            ));
        }

        return $proxy;
    }

    /**
     * @param $config
     * @return null
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function createProxyFromArray($config)
    {
        if (!is_array($config) || !isset($config['type'])) {
            throw new Exception\InvalidArgumentException(
                "Argument to %s must be an array containing attribute 'type'",
                __METHOD__);
        }

        /** @var $instance ProxyInterface */
        $instance = null;
        if (is_string($config['type'])) {
            $serviceLocator = $this->dataManager->getServiceLocator();
            $instance = $serviceLocator->get($config['type']);
        }

        if ($instance instanceof ProxyInterface) {
            $instance->setDataManager($this->dataManager);
            if (method_exists($instance, 'setFromArray')) {
                unset($config['type']);
                $instance->setFromArray($config);
            }
            return $instance;
        }

        throw new Exception\RuntimeException(
            "Could not create an instance of ProxyInterface by specified type (%s)",
            $config['type']);
    }

    /**
     * @param $proxies
     * @return mixed
     */
    public function setProxies($proxies)
    {
        $this->proxies = $proxies;
        return $this;
    }

    /**
     * @param DataManager $dataManager
     * @return ProxyManagerInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}