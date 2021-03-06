<?php

namespace ZfcDataManager\Store;

use \Traversable;
use ZfcDataManager\DataManager;

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
     * @return bool
     */
    public function hasStore($storeName)
    {
        return isset($this->stores[$storeName]);
    }

    /**
     * @param $storeName
     * @return mixed|Store
     */
    public function getStore($storeName)
    {
        return $this->loadStore($storeName);
    }

    /**
     * @param $modelName
     * @return mixed|null|Store|StoreInterface
     */
    public function getStoreByModelName($modelName)
    {
        foreach ($this->stores as $store => $config) {
            if (isset($config['model']) && $config['model'] == $modelName) {
                return $this->getStore($store);
            }
        }
        return null;
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
        $store = $this->createStore($storeName);
        $this->loadedStores[$storeName] = $store;

        return $store;
    }

    /**
     * @param $config
     * @return StoreInterface
     * @throws Exception\InvalidArgumentException
     */
    public function createStore($config)
    {
        if (is_string($config) && isset($this->stores[$config])) {
            return $this->createStoreFromArray($this->stores[$config]);
        } else if (is_array($config)) {
            return $this->createStoreFromArray($config);
        }

        throw new Exception\InvalidArgumentException(sprintf(
            "Argument passed to %s must reference or be a valid store configuration",
            __METHOD__
        ));
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
     * @return mixed|StoreManager
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}