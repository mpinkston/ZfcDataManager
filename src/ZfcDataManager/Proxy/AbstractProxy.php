<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManager;
use Zend\Stdlib\Options;

abstract class AbstractProxy extends Options implements ProxyInterface
{
    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @var string
     */
    public $mapping;

    /**
     * @param DataManager $dataManager
     * @return ProxyInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }

    /**
     * @param $mapping
     * @return mixed
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
        return $this;
    }
}