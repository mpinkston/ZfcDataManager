<?php

namespace ZfcDataManager\Model;

use \Traversable;
use ZfcDataManager\DataManager;
use ZfcDataManager\Model\Exception;

class ModelManager implements ModelManagerInterface
{
    /**
     * @var array
     */
    protected $models = array();

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @param $modelName
     * @return bool
     */
    public function hasModel($modelName)
    {
        return isset($this->models[$modelName]);
    }

    /**
     * @param $modelName
     * @return mixed|Model
     */
    public function getModel($modelName)
    {
        return $this->createModel($modelName);
    }

    /**
     * @param $config
     * @param array|null $data
     * @return mixed|ModelInterface
     * @throws Exception\InvalidArgumentException
     */
    public function createModel($config, array $data = null)
    {
        if (is_string($config) && isset($this->models[$config])) {
            $model = $this->createModelFromArray($this->models[$config]);
        } else if (is_array($config)) {
            $model = $this->createModelFromArray($config);
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                "Argument passed to %s must reference or be a valid model configuration",
                __METHOD__
            ));
        }

        if ($data) {
            $model->hydrate($data);
        }
        return $model;
    }

    /**
     * @param $config
     * @throws Exception\InvalidArgumentException
     * @return ModelInterface
     */
    public function createModelFromArray($config)
    {
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                "Argument passed to %s must be an array", __METHOD__);
        }

        $model = new Model();
        $model->setDataManager($this->dataManager)
            ->setFromArray($config);
        return $model;
    }

    /**
     * @param $models
     * @return mixed|ModelManager
     * @throws Exception\InvalidArgumentException
     */
    public function setModels($models)
    {
        if (is_array($models) || $models instanceof Traversable) {
            $this->models = $models;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter to %s\'s %s method must be an array or implement the \\Traversable interface',
                __CLASS__, __METHOD__
            ));
        }
        return $this;
    }

    /**
     * @param DataManager $dataManager
     * @return mixed
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}