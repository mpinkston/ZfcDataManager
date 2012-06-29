<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManagerAwareInterface;

interface FieldManagerInterface extends DataManagerAwareInterface
{
    /**
     * @abstract
     * @param $fieldName
     * @return boolean
     */
    public function hasField($fieldName);

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
     * @return array
     */
    public function getFields();

    /**
     * @abstract
     * @return mixed
     */
    public function getKeyMap();

    /**
     * @abstract
     * @return mixed
     */
    public function getDataMap();
}