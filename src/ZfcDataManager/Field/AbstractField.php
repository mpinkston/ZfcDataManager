<?php

namespace ZfcDataManager\Field;

use Zend\Validator\ValidatorInterface;
use Zend\Stdlib\Options;

abstract class AbstractField extends Options implements FieldInterface
{
    public $name;

    public $mapping;

    public $validator;

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
     * @param $validator
     * @return void|FieldInterface
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }
}