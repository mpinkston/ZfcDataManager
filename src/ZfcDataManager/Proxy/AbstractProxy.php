<?php

namespace ZfcDataManager\Proxy;

use ZfcDataManager\DataManager;
use ZfcDataManager\Model\ModelInterface;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractProxy extends AbstractOptions implements ProxyInterface
{
    /**
     * @var ModelInterface
     */
    public $model;

    /**
     * @var string
     */
    public $mapping;

    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @param $model
     * @return mixed|AbstractProxy
     */
    public function setModel($model)
    {
        $this->model = $model;
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

    /**
     * @param DataManager $dataManager
     * @return mixed|AbstractProxy
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}