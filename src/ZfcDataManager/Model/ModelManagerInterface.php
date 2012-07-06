<?php

namespace ZfcDataManager\Model;

use ZfcDataManager\DataManagerAwareInterface;

interface ModelManagerInterface extends DataManagerAwareInterface
{
    /**
     * @abstract
     * @param $models
     * @return mixed
     */
    public function setModels($models);

    /**
     * @abstract
     * @param $modelName
     * @return bool
     */
    public function hasModel($modelName);

    /**
     * @abstract
     * @param $modelName
     * @param array|null $data
     * @return ModelInterface
     */
    public function createModel($modelName, array $data = null);
}