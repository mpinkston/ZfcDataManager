<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManager;

// @TODO: I should validate/verify key names somewhere..

class FieldManager implements FieldManagerInterface
{
    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var array
     */
    public $loadedFields;

    /**
     * @param $fieldName
     * @return bool
     */
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function getField($fieldName)
    {
        return $this->loadField($fieldName);
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function loadField($fieldName)
    {
        if (isset($this->loadedFields[$fieldName])) {
            return $this->loadedFields[$fieldName];
        }

        if (isset($this->fields[$fieldName])) {
            $config = $this->fields[$fieldName];
            $config['name'] = $fieldName;
            $instance = $this->createFieldFromArray($config);
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                "No configuration could be found for specified field '%s' in %s",
                $fieldName,
                __METHOD__
            ));
        }

        if ($instance instanceof FieldInterface) {
            $this->loadedFields[$fieldName] = $instance;
        }
        return $instance;
    }

    /**
     * @param $config
     * @return FieldInterface
     */
    public function createFieldFromArray($config)
    {
        $type = isset($config['type'])?$config['type']:'default';
        unset($config['type']);

        if ($type == 'store') {
            $instance = new StoreField();
        } else if ($type == 'model') {
            $instance = new ModelField();
        } else {
            $instance = new Field();
        }

        $instance->setDataManager($this->dataManager);
        $instance->setFromArray($config);
        return $instance;
    }

    /**
     * @param $fields
     * @return mixed
     */
    public function setFields($fields)
    {
        if (!is_array($fields) && !($fields instanceof \Traversable)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "Argument to %s must be an array or implement Traversable",
                __METHOD__
            ));
        }

        $this->fields = array();
        foreach ($fields as $fieldName => $fieldConfig) {
            if (is_string($fieldName)) {
                $this->fields[$fieldName] = $fieldConfig;
            } else if (is_string($fieldConfig)) {
                $this->fields[$fieldConfig] = array();
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        foreach (array_keys($this->fields) as $fieldName) {
            $this->loadField($fieldName);
        }
        return $this->loadedFields;
    }

    /**
     * mapToFieldName: returns a field name from the provided field
     * mapping or field name.
     *
     * @param $key
     * @return null
     */
    public function mapToFieldName($key)
    {
        $keyMap = $this->getKeyMap();
        if ($eKey = array_search($key, $keyMap)) {
            return $eKey;
        } else if (isset($keyMap[$key])) {
            return $key;
        }
        return null;
    }

    /**
     * getKeyMap: returns a key => value array of field names and
     * the associated mapping
     *
     * @return array
     */
    public function getKeyMap()
    {
        $keyMap = array();
        /** @var $field FieldInterface */
        foreach ($this->getFields() as $field) {
            $keyMap[$field->getName()] = $field->getMapping();
        }
        return $keyMap;
    }

    /**
     * getDataMap: returns a key => value array of field names and
     * associated values (which should theoretically be in-sync with persistence
     * but not necessarily with the model's entity)
     *
     * @return array
     */
    public function getDataMap()
    {
        $dataMap = array();
        /** @var $field FieldInterface */
        foreach ($this->getFields() as $field) {
            $dataMap[$field->getName()] = $field->getValue();
        }
        return $dataMap;
    }

    /**
     * @param DataManager $dataManager
     * @return mixed|FieldManagerInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}