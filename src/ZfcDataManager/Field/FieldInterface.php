<?php

namespace ZfcDataManager\Field;

use Zend\Validator\ValidatorInterface;
use ZfcDataManager\DataManager;

interface FieldInterface
{
    /**
     * @abstract
     * @param array $data
     * @return mixed
     */
    public function getValue(array $data);

    public function setName($name);
    public function getName();

    public function setMapping($mapping);
    public function getMapping();

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return FieldInterface|AbstractField
     */
    public function setDataManager(DataManager $dataManager);
}