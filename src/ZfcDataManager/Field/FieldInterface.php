<?php

namespace ZfcDataManager\Field;

use Zend\Validator\ValidatorInterface;
use ZfcDataManager\DataManager;

interface FieldInterface
{
    public function setName($name);
    public function getName();

    public function setValue($value);
    public function hasValue();
    public function getValue();

    public function setMapping($mapping);
    public function getMapping();

    public function parseRecord(array $record);

    /**
     * @abstract
     * @param FieldManager $fieldManager
     * @return mixed
     */
    public function setFieldManager(FieldManager $fieldManager);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return FieldInterface|AbstractField
     */
    public function setDataManager(DataManager $dataManager);
}