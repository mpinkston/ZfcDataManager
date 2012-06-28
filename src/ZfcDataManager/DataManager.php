<?php

namespace ZfcDataManager;

use ZfcDataManager\Model\ModelManagerInterface;
use ZfcDataManager\Proxy\ProxyManagerInterface;
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
     * @param $storeName
     * @return Store\StoreInterface
     */
    public function getStore($storeName)
    {
        return $this->getStoreManager()->getStore($storeName);
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