<?php

namespace ZfcDataManager\Field;

use ZfcDataManager\DataManager;

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
            $instance = new Type\StringField();
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
        // @TODO: find a better way to determine and instantiate appropriate field type
        // somehow, switch statements now look ugly to me..

        $type = isset($config['type'])?$config['type']:'default';
        unset($config['type']);

        switch ($type) {
            case 'int':
            case 'integer':
                $instance = new Type\IntegerField();
                break;

            case 'model':
                $instance = new Type\ModelField();
                break;

            case 'store':
                $instance = new Type\StoreField();
                break;

            case 'string':
            default:
                $instance = new Type\StringField();
                break;

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
        $this->fields = $fields;
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

    // @TODO: I should validate/verify key names somewhere..

    /**
     * getKeyMap: returns a key => value array of field names
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
     * @param DataManager $dataManager
     * @return FieldManagerInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}