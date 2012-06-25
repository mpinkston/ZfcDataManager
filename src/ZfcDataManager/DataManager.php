<?php

namespace ZfcDataManager;

use ZfcDataManager\Exception;
use ZfcDataManager\Model\ModelManager;
use ZfcDataManager\Model\ModelManagerInterface;
use ZfcDataManager\Store\StoreManager;
use ZfcDataManager\Store\StoreManagerInterface;
use ZfcDataManager\Proxy\ProxyManager;
use ZfcDataManager\Proxy\ProxyManagerInterface;
use Zend\Stdlib\Options;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\Exception\InvalidServiceNameException;

class DataManager extends Options implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var string|StoreManagerInterface
     */
    protected $storeManager = 'StoreManager';

    /**
     * @var string|ModelManagerInterface
     */
    protected $modelManager = 'ModelManager';

    /**
     * @var string|ProxyManagerInterface
     */
    protected $proxyManager = 'ProxyManager';


    /**
     * @param array $models
     * @throws Exception\InvalidArgumentException
     * @return DataManager
     */
    public function setModels($models)
    {
        $this->getModelManager()->setModels($models);
        return $this;
    }

    /**
     * @param array $data
     * @param $modelName
     * @return mixed|void
     */
    public function createModel(array $data, $modelName)
    {
        return $this->getModelManager()->createModel($data, $modelName);
    }

    /**
     * @param array $stores
     * @throws Exception\InvalidArgumentException
     * @return DataManager
     */
    public function setStores($stores)
    {
        $this->getStoreManager()->setStores($stores);
        return $this;
    }

    /**
     * @param $storeName
     * @return mixed
     */
    public function getStore($storeName)
    {
        return $this->getStoreManager()->getStore($storeName);
    }

    /**
     * @param $proxies
     * @return DataManager
     */
    public function setProxies($proxies)
    {
        $this->getProxyManager()->setProxies($proxies);
        return $this;
    }

    /**
     * @param $modelManager
     * @return DataManager
     * @throws Exception\InvalidArgumentException
     */
    public function setModelManager(ModelManagerInterface $modelManager)
    {
        if (is_string($modelManager) || $modelManager instanceof ModelManagerInterface) {
            $this->modelManager = $modelManager;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the StoreManagerInterface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return ModelManager
     */
    public function getModelManager()
    {
        if (!$this->modelManager instanceof ModelManagerInterface) {
            if (is_string($this->modelManager)) {
                $instance = $this->getServiceLocator()->get($this->modelManager);
                if (!$instance instanceof ModelManagerInterface) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        'ServiceLocator failed to return an instance of ModelManagerInterface'
                    ));
                }
            } else {
                $instance = new ModelManager();
            }
            $instance->setDataManager($this);
            $this->modelManager = $instance;
        }
        return $this->modelManager;
    }

    /**
     * @param StoreManagerInterface $storeManager
     * @throws Exception\InvalidArgumentException
     * @return DataManager
     */
    public function setStoreManager(StoreManagerInterface $storeManager)
    {
        if (is_string($storeManager) || $storeManager instanceof StoreManagerInterface) {
            $this->storeManager = $storeManager;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the StoreManagerInterface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        if (!$this->storeManager instanceof StoreManagerInterface) {
            if (is_string($this->storeManager)) {
                $instance = $this->getServiceLocator()->get($this->storeManager);
                if (!$instance instanceof StoreManagerInterface) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        'ServiceLocator failed to return an instance of StoreManagerInterface'
                    ));
                }
            } else {
                $instance = new StoreManager();
            }
            $instance->setDataManager($this);
            $this->storeManager = $instance;
        }
        return $this->storeManager;
    }

    /**
     * @param ProxyManagerInterface $proxyManager
     * @throws Exception\InvalidArgumentException
     * @return DataManager
     */
    public function setProxyManager(ProxyManagerInterface $proxyManager)
    {
        if (is_string($proxyManager) || $proxyManager instanceof ProxyManagerInterface) {
            $this->proxyManager = $proxyManager;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the ProxyManagerInterface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }

    /**
     * @return ProxyManagerInterface
     * @throws Exception\InvalidArgumentException
     */
    public function getProxyManager()
    {
        if (!$this->proxyManager instanceof ProxyManagerInterface) {
            if (is_string($this->proxyManager)) {
                $instance = $this->getServiceLocator()->get($this->proxyManager);
                if (!$instance instanceof ProxyManagerInterface) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        'ServiceLocator failed to return an instance of ProxyManagerInterface'
                    ));
                }
            } else {
                $instance = new ProxyManager();
            }
            $instance->setDataManager($this);
            $this->proxyManager = $instance;
        }
        return $this->proxyManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return DataManager
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}