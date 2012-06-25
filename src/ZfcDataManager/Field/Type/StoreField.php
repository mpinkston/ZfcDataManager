<?php

namespace ZfcDataManager\Field\Type;

use ZfcDataManager\Field\AbstractField;
use ZfcDataManager\Store\AbstractStore;

class StoreField extends AbstractField
{
    public $primary_key;

    public $foreign_key;

    /**
     * @param array $data
     * @return mixed|null|AbstractStore
     */
    public function getValue(array $data)
    {
        $storeName = $this->getMapping();
        $store = $this->dataManager->createStore($storeName);

        $fKey = $this->getForeignKey();
        $pKey = $this->getPrimaryKey();

        // @TODO: some validation on keys.
        if (isset($data[$pKey])) {
            $store->setFilters(array(
                $fKey => $data[$pKey]
            ));
        }
        return $store;
    }

    /**
     * @param $foreign_key
     */
    public function setForeignKey($foreign_key)
    {
        $this->foreign_key = $foreign_key;
    }

    public function getForeignKey()
    {
        return $this->foreign_key;
    }

    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
    }

    public function getPrimaryKey()
    {
        return $this->primary_key;
    }
}