<?php

namespace ZfcDataManager;

use ZfcDataManager\Model\ModelManagerInterface;
use ZfcDataManager\Proxy\ProxyManagerInterface;
use ZfcDataManager\Store\StoreInterface;
use ZfcDataManager\Store\StoreManagerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\AbstractOptions;

class DataManager implements ServiceManagerAwareInterface
{
    /**
     * @var array
     */
    protected $configuration = null;

    /**
     * @var string|ModelManagerInterface
     */
    protected $modelManager = 'ModelManager';

    /**
     * @var string|ProxyManagerInterface
     */
    protected $proxyManager = 'ProxyManager';

    /**
     * @var string|StoreManagerInterface
     */
    protected $storeManager = 'StoreManager';

    /**
     * @param $configuration
     * @param ServiceManager $serviceManager
     */
    public function __construct($configuration, ServiceManager $serviceManager)
    {
        $this->configuration  = $configuration;
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (preg_match("/^get(?<name>[a-z0-9]+)/i", $name, $match)) {

            // @TODO: name canonicalization
            $myName = strtolower($match['name']);

            if ($this->getStoreManager()->hasStore($myName)) {
                return $this->getStore($myName);
            } else if ($this->getModelManager()->hasModel($myName)) {
                if (count($arguments) == 1) {
                    if (is_array($arguments[0])) {
                        return $this->createModel($myName, $arguments[0]);
                    } else if (is_scalar($arguments[0])) {
                        return $this->getModel($myName, $arguments[0]);
                    }
                } else {
                    return $this->createModel($myName);
                }
            }
        }
        return null;
    }

    /**
     * @param $storeName
     * @return Store\StoreInterface
     */
    public function getStore($storeName)
    {
        return $this->getStoreManager()->getStore($storeName);
    }

    /**
     * @param $modelName
     * @param $id
     * @return mixed
     */
    public function getModel($modelName, $id)
    {
        $store = $this->getStoreManager()->getStoreByModelName($modelName);
        if ($store instanceof StoreInterface) {
            return $store->getById($id);
        }
        return null;
    }

    /**
     * @param $modelName
     * @param array|null $data
     * @return Model\ModelInterface
     */
    public function createModel($modelName, array $data = null)
    {
        return $this->getModelManager()->createModel($modelName, $data);
    }

    /**
     * @return string|Model\ModelManagerInterface
     */
    public function getModelManager()
    {
        if (!$this->modelManager instanceof ModelManagerInterface) {
            if (is_string($this->modelManager)){
                $this->modelManager = $this->getServiceManager()->get($this->modelManager);
                $this->modelManager->setDataManager($this);
                $this->modelManager->setModels($this->configuration['models']);
            }
        }
        return $this->modelManager;
    }

    /**
     * @return string|Proxy\ProxyManagerInterface
     */
    public function getProxyManager()
    {
        if (!$this->proxyManager instanceof ProxyManagerInterface) {
            if (is_string($this->proxyManager)) {
                $this->proxyManager = $this->getServiceManager()->get($this->proxyManager);
                $this->proxyManager->setDataManager($this);
                $this->proxyManager->setProxies($this->configuration['proxies']);
            }
        }
        return $this->proxyManager;
    }

    /**
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        if (!$this->storeManager instanceof StoreManagerInterface) {
            if (is_string($this->storeManager)) {
                $this->storeManager = $this->getServiceManager()->get($this->storeManager);
                $this->storeManager->setDataManager($this);
                $this->storeManager->setStores($this->configuration['stores']);
            }
        }
        return $this->storeManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return DataManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}