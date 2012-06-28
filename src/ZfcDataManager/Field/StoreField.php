<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\Field\AbstractField;
use ZfcDataManager\Store\AbstractStore;

class StoreField extends AbstractField
{
    /**
     * @var string
     */
    public $primary_key;

    /**
     * @var string
     */
    public $foreign_key;

    /**
     * @var string
     */
    public $store_name;

    /**
     * @param array $record
     * @return StoreField|AbstractField
     */
    public function parseRecord(array $record)
    {
        $storeName = $this->getStoreName();
        $storeManager = $this->dataManager->getStoreManager();
        $store = $storeManager->createStore($storeName);

        $fKey = $this->getForeignKey();
        $pKey = $this->getPrimaryKey();

        if ($fKey && isset($record[$pKey])) {
            $store->setFilters(array(
                $fKey => $record[$pKey]
            ));
        }
        $this->setValue($store);
        return $this;
    }

    /**
     * @param $foreign_key
     * @return StoreField
     */
    public function setForeignKey($foreign_key)
    {
        $this->foreign_key = $foreign_key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForeignKey()
    {
        return $this->foreign_key;
    }

    /**
     * @param $primary_key
     * @return StoreField
     */
    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * @param string $store_name
     * @return AbstractField|StoreField
     */
    public function setStoreName($store_name)
    {
        $this->store_name = $store_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->store_name;
    }
}