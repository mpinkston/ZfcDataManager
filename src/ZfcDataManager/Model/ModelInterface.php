<?php

namespace ZfcDataManager\Model;

use ZfcDataManager\DataManager;
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
     * @param DataManager $dataManager
     * @return ModelInterface
     */
    public function setDataManager(DataManager $dataManager);
}