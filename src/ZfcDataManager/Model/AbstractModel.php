<?php

namespace ZfcDataManager\Model;

use ArrayObject;
use ZfcDataManager\DataManager;
use ZfcDataManager\Field\FieldInterface;
use ZfcDataManager\Field\FieldManager;
use ZfcDataManager\Field\FieldManagerInterface;
use ZfcDataManager\Store\StoreInterface;
use Zend\Stdlib\Options;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractModel extends Options implements ModelInterface
{
    /**
     * @var
     */
    protected $entity;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var string|HydratorInterface
     */
    protected $hydrator = 'Zend\Stdlib\Hydrator\ObjectProperty';

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @var FieldManager
     */
    protected $fieldManager = 'FieldManager';

    /**
     * @var StoreInterface
     */
    protected $parentStore;

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        $entity = $this->getEntity();
        if (method_exists($entity, $name)) {
            return call_user_func_array(array($entity, $name), $arguments);
        }

        throw new Exception\InvalidArgumentException(sprintf(
            "Failed to proxy method call (%s) to entity (%s)",
            $name,
            get_class($entity)
        ));
    }

    /**
     * @param string $name
     * @return mixed|void
     */
    public function __get($name)
    {
        $entity = $this->getEntity();
        if (property_exists($entity, $name)) {
            return $entity->{$name};
        }
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $entity = $this->getEntity();
        if (property_exists($entity, $name)) {
            $entity->{$name} = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * hydrate: Maps data. $data should be in the exact same
     * format as returned by the proxy at this point.
     *
     * @param array $data
     * @return mixed|Model|ModelInterface
     */
    public function hydrate(array $data)
    {
        $fieldManager = $this->getFieldManager();

        // $dataMap should be a name => value array where the name is always
        // the field name as specified in the config, and the value is determined
        // by the field type and the contents of $data.
        $dataMap = array();

        /** @var $field FieldInterface */
        foreach ($fieldManager->getFields() as $name => $field) {
            $dataMap[$name] = $field->getValue($data);
        }

        $hydrator = $this->getHydrator();
        $hydrator->hydrate($dataMap, $this->getEntity());
        return $this;
    }

    /**
     * @param string|HydratorInterface $hydrator
     * @return AbstractModel|ModelInterface
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator instanceof HydratorInterface) {
            if (is_string($this->hydrator)) {
                $serviceLocator = $this->dataManager->getServiceLocator();
                $this->hydrator = $serviceLocator->get($this->hydrator);
            }
        }
        return $this->hydrator;
    }

    /**
     * @param $entity
     * @return mixed|void
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return mixed|void
     */
    public function getEntity()
    {
        if (is_string($this->entity)) {
            if (class_exists($this->entity)) {
                $this->entity = new $this->entity();
            } else {
                $locator = $this->dataManager->getServiceLocator();
                $this->entity = $locator->get($this->entity);
            }
        }
        if (!is_object($this->entity)) {
            $this->entity = new \stdClass;
        }
        return $this->entity;
    }

    /**
     * @return string|FieldManager
     * @throws Exception\InvalidArgumentException
     */
    public function getFieldManager()
    {
        if (!$this->fieldManager instanceof FieldManagerInterface) {
            if (is_string($this->fieldManager)) {
                $serviceLocator = $this->dataManager->getServiceLocator();
                $instance = $serviceLocator->get($this->fieldManager);
                if (!$instance instanceof FieldManagerInterface) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        'ServiceLocator failed to return an instance of FieldManagerInterface in %s',
                        __METHOD__
                    ));
                }
            } else {
                $instance = new FieldManager();
            }
            $instance->setDataManager($this->dataManager);
            $this->fieldManager = $instance;
        }
        return $this->fieldManager;
    }

    /**
     * @param $fieldName
     * @return FieldInterface
     */
    public function getField($fieldName)
    {
        return $this->getFieldManager()->getField($fieldName);
    }

    /**
     * @param $fields
     * @return AbstractModel
     */
    public function setFields($fields)
    {
        $this->getFieldManager()->setFields($fields);
        return $this;
    }

    /**
     * @param StoreInterface $store
     */
    public function setParentStore(StoreInterface $store)
    {
        $this->parentStore = $store;
    }

    /**
     * @param DataManager $dataManager
     * @return AbstractModel|ModelInterface
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void|ModelInterface
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class()
        ));
        $this->events = $eventManager;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function events()
    {
        if (!$this->events()) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}