<?php

namespace ZfcDataManager\Model;

use ZfcDataManager\DataManager;
use ZfcDataManager\Model\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModelManager implements ModelManagerInterface
{
    /**
     * @var array
     */
    protected $models;

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @param array $data
     * @param $modelName
     * @return mixed|Model
     * @throws Exception\RuntimeException
     */
    public function createModel(array $data, $modelName)
    {
        if (isset($this->models[$modelName])) {
            $model = $this->createModelFromArray($this->models[$modelName]);
        } else {
            // @TODO: evaluate if this should be here..
            $model = new Model();
        }

        if (!$model instanceof ModelInterface) {
            throw new Exception\RuntimeException(sprintf(
                "Failed to create model instance for %s in %s",
                $modelName,
                __METHOD__
            ));
        }
        $model->hydrate($data);
        return $model;
    }

    /**
     * @param $config
     * @return Model
     */
    public function createModelFromArray($config)
    {
        $model = new Model();
        $model->setDataManager($this->dataManager)
            ->setFromArray($config);
        return $model;
    }

    /**
     * @param $models
     * @return mixed|ModelManager
     */
    public function setModels($models)
    {
        $this->models = $models;
        return $this;
    }

    /**
     * @param DataManager $dataManager
     * @return ModelManager|ModelManagerInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}