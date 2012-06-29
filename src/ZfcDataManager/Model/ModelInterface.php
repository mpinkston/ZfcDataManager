<?php

namespace ZfcDataManager\Model;

use ZfcDataManager\DataManager;
use ZfcDataManager\Store\StoreInterface;
use ZfcDataManager\Field\FieldInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

interface ModelInterface extends EventManagerAwareInterface
{
    /**
     * @abstract
     * @param string|HydratorInterface $hydrator
     * @return ModelInterface
     */
    public function setHydrator($hydrator);

    /**
     * @abstract
     * @return HydratorInterface
     */
    public function getHydrator();

    /**
     * @abstract
     * @param array $data
     * @return mixed
     */
    public function hydrate(array $data);

    /**
     * @abstract
     * @return array
     */
    public function extract();

    /**
     * @abstract
     * @param $entity
     * @return mixed
     */
    public function setEntity($entity);

    /**
     * @abstract
     * @return mixed
     */
    public function getEntity();

    /**
     * @abstract
     * @return boolean
     */
    public function hasIdField();

    /**
     * @abstract
     * @return FieldInterface
     */
    public function getIdField();

    /**
     * @abstract
     * @param StoreInterface $storeInterface
     * @return ModelInterface
     */
    public function setParentStore(StoreInterface $storeInterface);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return ModelInterface
     */
    public function setDataManager(DataManager $dataManager);
}