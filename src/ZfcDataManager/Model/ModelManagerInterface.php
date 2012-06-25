<?php

namespace ZfcDataManager\Model;

use ZfcDataManager\DataManager;

/**
 *
 */
interface ModelManagerInterface
{
    /**
     * @abstract
     * @param array $data
     * @param $modelName
     * @return mixed
     */
    public function createModel(array $data, $modelName);

    /**
     * @abstract
     * @param $models
     * @return mixed
     */
    public function setModels($models);

    /**
     * @abstract
     * @param DataManager $dataManager
     * @return ModelManagerInterface
     */
    public function setDataManager(DataManager $dataManager);
}