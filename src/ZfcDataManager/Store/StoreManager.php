<?php

namespace ZfcDataManager\Store;

use \Traversable;
use ZfcDataManager\DataManager;
use ZfcDataManager\Store\Store;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 */
class StoreManager implements StoreManagerInterface
{
    /**
     * @var array
     */
    protected $stores = array();

    /**
     * @var array
     */
    protected $loadedStores = array();

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @param $storeName
     * @return mixed|Store
     */
    public function getStore($storeName)
    {
        return $this->loadStore($storeName);
    }

    /**
     * @param $storeName
     * @return Store
     * @throws Exception\InvalidArgumentException
     */
    public function loadStore($storeName)
    {
        if (isset($this->loadedStores[$storeName])) {
            return $this->loadedStores[$storeName];
        }

        if (isset($this->stores[$storeName])) {
            $store = $this->createStoreFromArray($this->stores[$storeName]);
            $this->loadedStores[$storeName] = $store;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                "No instance or configuration could be found for the specified store (%s)",
                $storeName
            ));
        }

        return $store;
    }

    /**
     * @param $config
     * @throws Exception\InvalidArgumentException
     * @return StoreInterface
     */
    public function createStoreFromArray($config)
    {
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                "Argument passed to %s must be an array", __METHOD__);
        }

        // @TODO check for the type of store to create
        $store = new Store();
        $store->setDataManager($this->dataManager)
            ->setFromArray($config);
        return $store;
    }

    /**
     * @param $stores
     * @return mixed|StoreManager
     * @throws Exception\InvalidArgumentException
     */
    public function setStores($stores)
    {
        if (is_array($stores) || $stores instanceof Traversable) {
            $this->stores = $stores;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the \\Traversable interface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }

    /**
     * @param DataManager $dataManager
     * @return StoreManagerInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}