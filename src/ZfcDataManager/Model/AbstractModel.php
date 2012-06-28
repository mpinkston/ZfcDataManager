<?php

namespace ZfcDataManager\Model;

use ArrayObject;
use ZfcDataManager\DataManager;
use ZfcDataManager\Field\FieldInterface;
use ZfcDataManager\Field\FieldManager;
use ZfcDataManager\Field\FieldManagerInterface;
use ZfcDataManager\Store\AbstractStore;
use ZfcDataManager\Store\StoreInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractModel extends AbstractOptions implements ModelInterface
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
     * whether or not this model has been hydrated.
     * @var bool
     */
    protected $hydrated = false;

    /**
     * If true, automatically (lazily) hydrate this model if the
     * id property exists, has been set, and if there's a parent store.
     *
     * @var bool
     */
    protected $auto_hydrate = true;

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @var FieldManager
     */
    protected $fieldManager = 'FieldManager';

    /**
     * @var string
     */
    protected $idProperty = 'id';

    /**
     * @var \ZfcDataManager\Store\AbstractStore
     */
    protected $parentStore;

    /**
     * This allows lazy-loading models
     */
    protected function autoHydrate()
    {
        if (!$this->hydrated && $this->auto_hydrate === true) {
            /** @var $idField FieldInterface */
            $idField = $this->getField($this->idProperty);
            if ($idField && $id = $idField->getValue()) {
                $this->load($id);
            } else {
                // @TODO: this is a temporary hack to initialize the fields if no data can be loaded
                $dataMap = $this->fieldManager->getDataMap();
                $this->hydrate($dataMap);
            }
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        $this->autoHydrate();
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
        $this->autoHydrate();
        $entity = $this->getEntity();
        if (property_exists($entity, $name)) {
            return $entity->{$name};
        } else {
            return parent::__get($name);
        }
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
     * @param $id
     * @return AbstractModel
     */
    public function load($id)
    {
        $proxy = $this->parentStore->getProxyForRead();
        $record = $proxy->read($id);
        $this->hydrate($record);
        return $this;
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

        /** @var $field FieldInterface */
        foreach ($fieldManager->getFields() as $field) {
            $field->parseRecord($data);
        }

        $hydrator = $this->getHydrator();
        $hydrator->hydrate(
            $fieldManager->getDataMap(),
            $this->getEntity()
        );
        $this->hydrated = true;
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
                $serviceManager = $this->dataManager->getServiceManager();
                $this->hydrator = $serviceManager->get($this->hydrator);
            }
        }
        return $this->hydrator;
    }

    /**
     * @return boolean
     */
    public function isHydrated()
    {
        return $this->hydrated;
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
                $serviceManager = $this->dataManager->getServiceManager();
                $this->entity = $serviceManager->get($this->entity);
            }
        }
        if (!is_object($this->entity)) {
            $this->entity = new \stdClass;
        }
        return $this->entity;
    }

    /**
     * @return FieldManagerInterface
     * @throws Exception\InvalidArgumentException
     */
    public function getFieldManager()
    {
        if (!$this->fieldManager instanceof FieldManagerInterface) {
            if (is_string($this->fieldManager)) {
                $serviceManager = $this->dataManager->getServiceManager();
                $instance = $serviceManager->get($this->fieldManager);
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
     * @return \ZfcDataManager\Field\FieldInterface
     */
    public function getIdField()
    {
        return $this->getField($this->idProperty);
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
     * @param $idProperty
     * @return AbstractModel
     */
    public function setIdProperty($idProperty)
    {
        $this->idProperty = $idProperty;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdProperty()
    {
        return $this->idProperty;
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

    /**
     * @param $auto_hydrate
     * @return AbstractModel
     */
    public function setAutoHydrate($auto_hydrate)
    {
        $this->auto_hydrate = $auto_hydrate;
        return $this;
    }
}