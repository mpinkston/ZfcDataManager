<?php

namespace ZfcDataManager\Field;

use Zend\Validator\ValidatorInterface;

interface FieldInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getName();

    /**
     * @abstract
     * @return string
     */
    public function getMapping();

    /**
     * @abstract
     * @param $validator
     * @return FieldInterface
     */
    public function setValidator($validator);

    /**
     * @abstract
     * @return ValidatorInterface
     */
    public function getValidator();
}