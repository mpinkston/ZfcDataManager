<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManagerAwareInterface;

interface FieldManagerInterface extends DataManagerAwareInterface
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
}