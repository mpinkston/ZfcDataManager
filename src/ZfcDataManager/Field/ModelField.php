<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\Field\AbstractField;
use ZfcDataManager\Store\AbstractStore;

class ModelField extends StoreField
{
    /**
     * @param array $record
     * @return StoreField|AbstractField
     */
    public function parseRecord(array $record)
    {
        $storeName = $this->getStoreName();
        $store = $this->dataManager->getStore($storeName);
        $model = $store->createModel();

        $pKey = $this->getPrimaryKey();

        if (isset($record[$pKey])) {
            $idField = $model->getIdField();
            $idField->setValue($record[$pKey]);
        }

        $this->setValue($model);
        return $this;
    }
}