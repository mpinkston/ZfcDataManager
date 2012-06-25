<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManager;

interface FieldManagerInterface
{
    /**
     * @abstract
     * @param $fieldName
     * @return mixed
     */
    public function getField($fieldName);

    /**
     * @abstract
     * @param $fields
     * @return mixed
     */
    public function setFields($fields);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return FieldManagerInterface
     */
    public function setDataManager(DataManager $dataManager);
}