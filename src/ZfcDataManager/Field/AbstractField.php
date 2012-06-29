<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManager;
use Zend\Validator\ValidatorInterface;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractField extends AbstractOptions implements FieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $mapping;

    /**
     * @var FieldManager
     */
    protected $fieldManager;

    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @param array $record
     * @return AbstractField
     */
    public function parseRecord(array $record)
    {
        $mapping = $this->getMapping();
        if (isset($record[$mapping])) {
            $this->setValue($record[$mapping]);
        }
        return $this;
    }

    /**
     * @param $value
     * @return AbstractField
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function hasValue()
    {
        return $this->value !== null;
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
     * @param FieldManager $fieldManager
     * @return mixed
     */
    public function setFieldManager(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
        return $this;
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