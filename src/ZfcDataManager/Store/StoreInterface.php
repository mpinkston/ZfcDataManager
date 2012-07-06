<?php

namespace ZfcDataManager\Store;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use ZfcDataManager\Model\ModelInterface;

interface StoreInterface extends EventManagerAwareInterface
{
    public function getData();

    public function loadData(array $data);

    /**
     * @abstract
     * @param array $options
     * @return mixed
     */
    public function load(array $options = null);

    /**
     * @abstract
     * @param ModelInterface $model
     * @return StoreInterface
     */
    public function add(ModelInterface $model);

    /**
     * @abstract
     * @param string $model
     * @return StoreInterface
     */
    public function setModel($model);

    /**
     * @abstract
     * @return string
     */
    public function getModel();

    /**
     * @abstract
     * @param array|null $record
     * @return ModelInterface
     */
    public function createModel(array $record = null);

    /**
     * @abstract
     * @return int
     */
    public function getPageRange();

    /**
     * @abstract
     * @return int
     */
    public function getCurrentPage();

}