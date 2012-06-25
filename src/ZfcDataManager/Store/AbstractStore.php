<?php

namespace ZfcDataManager\Store;

use \ArrayObject;
use \Traversable;
use ZfcDataManager\DataManager;
use ZfcDataManager\Model\ModelManager;
use ZfcDataManager\Model\ModelInterface;
use ZfcDataManager\Proxy\ProxyInterface;
use ZfcDataManager\Proxy\ReadableProxyInterface;
use ZfcDataManager\Proxy\SortableProxyInterface;
use ZfcDataManager\Proxy\FilterableProxyInterface;
use ZfcDataManager\Proxy\WritableProxyInterface;
use ZfcDataManager\Proxy\Exception\RuntimeException;
use Zend\Stdlib\Options;
use Zend\Stdlib\ArrayUtils;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractStore extends Options
    implements StoreInterface
{
    /**
     * @var EventManager
     */
    protected $events;

    /**
     * @var bool
     */
    protected $autoLoad = true;

    /**
     * @var ArrayObject
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $proxy;

    /**
     * @var string
     */
    protected $mapping;

    /**
     * @var mixed
     */
    protected $sorters;

    /**
     * @var mixed
     */
    protected $filters;

    /**
     * @var int
     */
    protected $startIndex = 0;

    /**
     * @var int
     */
    protected $pageSize = 50;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @param $record
     * @return ModelInterface
     */
    public function add($record)
    {
        $model = $this->createModel($record);
        if (!$this->data) {
            $this->data = new ArrayObject();
        }
        $this->data->append($model);
    }

    /**
     * @param null|array $options
     * @throws \ZfcDataManager\Proxy\Exception\RuntimeException
     * @return \ArrayObject
     */
    public function load($options = null)
    {
        if (null !== $options) {
            $this->setFromArray($options);
        }

        $proxy = $this->getProxyForRead();

        if ($proxy instanceof SortableProxyInterface) {
            $proxy->setSortBy($this->getSorters());
        }

        if ($proxy instanceof FilterableProxyInterface) {
            $proxy->setFilterBy($this->getFilters());
        }

        $data = $proxy->fetch(
            $this->getStartIndex(),
            $this->getPageSize()
        );

        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $record) {
                $this->add($record);
            }
        } else {
            throw new RuntimeException("Proxy failed to return an iterable result");
        }

        return $this;
    }

    /**
     * @param $data
     * @throws Exception\InvalidArgumentException
     * @return StoreInterface|AbstractStore
     */
    public function loadData($data)
    {
        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $record) {
                $this->add($record);
            }
            return $this;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            "Argument to %s must be an array or instance of Traversable",
            __METHOD__
        ));
    }

    /**
     * @param null $sorters
     */
    public function sort($sorters = null)
    {

    }

    /**
     * @param null $filters
     */
    public function filter($filters = null)
    {

    }

    /**
     * @param $data
     * @throws Exception\InvalidArgumentException
     * @return StoreInterface|AbstractStore
     */
    public function setData($data)
    {
        return $this->loadData($data);
    }

    /**
     * @return \ArrayObject
     */
    public function getData()
    {
        if (!$this->data && $this->autoLoad) {
            $this->load();
        }
        return $this->data;
    }

    /**
     * @param $model
     * @return string
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $record
     * @return ModelInterface|void
     */
    public function createModel($record)
    {
        if (!$record instanceof ModelInterface) {
            $modelManager = $this->dataManager->getModelManager();
            $record = $modelManager->createModel($record, $this->model);
            $record->setParentStore($this);
        }
        return $record;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        if ($this->totalCount === null) {
            $proxy = $this->getProxyForRead();
            $this->totalCount = $proxy->getTotalCount();
        }
        return $this->totalCount;
    }

    /**
     * @param int $startIndex
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }

    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param $proxy
     * @return StoreInterface|AbstractStore
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        if (!$this->proxy instanceof ProxyInterface) {
            $proxyManager = $this->dataManager->getProxyManager();
            $proxy = $proxyManager->getProxy($this->proxy);
            $proxy->setMapping($this->getMapping());
            $this->proxy = $proxy;
        }
        return $this->proxy;
    }

    /**
     * @throws Exception\UnsupportedOperationException
     * @return ReadableProxyInterface|FilterableProxyInterface|SortableProxyInterface
     */
    public function getProxyForRead()
    {
        $proxy = $this->getProxy();
        if (!$proxy instanceof ReadableProxyInterface) {
            throw new Exception\UnsupportedOperationException(sprintf(
                "This store has not been configured with a readable proxy"
            ));
        }
        return $proxy;
    }

    /**
     * @throws Exception\UnsupportedOperationException
     * @return WritableProxyInterface
     */
    public function getProxyForWrite()
    {
        $proxy = $this->getProxy();
        if (!$proxy instanceof WritableProxyInterface) {
            throw new Exception\UnsupportedOperationException(sprintf(
                "This store has not been configured with a writable proxy"
            ));
        }
        return $proxy;
    }

    /**
     * Inject an EventManager instance
     *
     * @param EventManagerInterface $eventManager
     * @return void|StoreInterface
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
     * @param mixed $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $sorters
     */
    public function setSorters($sorters)
    {
        $this->sorters = $sorters;
    }

    /**
     * @return mixed
     */
    public function getSorters()
    {
        return $this->sorters;
    }

    /**
     * @param string $mapping
     * @return \ZfcDataManager\Store\AbstractStore
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param DataManager $dataManager
     * @return StoreManagerInterface|AbstractStore
     */
    public function setDataManager(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}