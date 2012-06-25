<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManager;
use Zend\Validator\ValidatorInterface;
use Zend\Stdlib\Options;

abstract class AbstractField extends Options implements FieldInterface
{
    public $name;

    public $mapping;

    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @param array $data
     * @return mixed|null
     */
    public function getValue(array $data)
    {
        if (isset($data[$this->getMapping()])) {
            return $data[$this->getMapping()];
        }
        return null;
    }

    /**
     * @param $name
     * @return AbstractField
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $mapping
     * @return AbstractField
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * @return string|void
     */
    public function getMapping()
    {
        return $this->mapping?$this->mapping:$this->name;
    }

    /**
     * @param DataManager $dataManager
     * @return AbstractField|FieldInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}