<?php

namespace ZfcDataManager\Store;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

interface StoreInterface extends EventManagerAwareInterface
{
    public function getData();
    public function load($options = null);
    public function loadData($data);

    public function setModel($model);
    public function getModel();
}