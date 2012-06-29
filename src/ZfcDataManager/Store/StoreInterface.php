<?php

namespace ZfcDataManager\Store;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use ZfcDataManager\Model\ModelInterface;

interface StoreInterface extends EventManagerAwareInterface
{
    public function getData();
    public function load($options = null);
    public function loadData($data);

    public function setModel($model);
    public function getModel();

    /**
     * @abstract
     * @param array|null $record
     * @return ModelInterface
     */
    public function createModel(array $record = null);
}