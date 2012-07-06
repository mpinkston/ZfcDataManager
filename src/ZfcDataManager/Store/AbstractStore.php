<?php

namespace ZfcDataManager\Store;

use \ArrayObject;
use \Traversable;
use \IteratorAggregate;
use ZfcDataManager\DataManager;
use ZfcDataManager\Model\ModelManager;
use ZfcDataManager\Model\ModelInterface;
use ZfcDataManager\Proxy\ProxyInterface;
use ZfcDataManager\Proxy\ReadableProxyInterface;
use ZfcDataManager\Proxy\SortableProxyInterface;
use ZfcDataManager\Proxy\FilterableProxyInterface;
use ZfcDataManager\Proxy\WritableProxyInterface;
use ZfcDataManager\Proxy\Exception\RuntimeException;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractStore extends AbstractOptions
    implements StoreInterface, IteratorAggregate
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
     * @var ProxyInterface
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
    protected $totalCount = 0;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var int
     */
    protected $startIndex = 0;

    /**
     * @var int
     */
    protected $pageSize = 50;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @param ModelInterface $model
     * @return StoreInterface
     */
    public function add(ModelInterface $model)
    {
        if (!$this->data instanceof ArrayObject) {
            $this->data = new ArrayObject();
        }
        $this->data->append($model);
    }

    /**
     * @param array $options
     * @return mixed|AbstractStore
     * @throws Exception\RuntimeException
     */
    public function load(array $options = null)
    {
        if (null !== $options) {
            $this->setFromArray($options);
        }

        $proxy = $this->getProxyForRead();
        $data = $proxy->fetch(
            $this->getStartIndex(),
            $this->getPageSize()
        );
        $this->loadData($data);

        return $this;
    }

    /**
     * @param $data
     * @throws Exception\InvalidArgumentException
     * @return StoreInterface|AbstractStore
     */
    public function loadData(array $data)
    {
        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $record) {
                if ($record instanceof ModelInterface) {
                    $this->add($record);
                } else if (ArrayUtils::hasStringKeys($record)) {
                    $this->add($this->createModel($record));
                }
            }
            return $this;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            "Argument to %s must be an array or instance of Traversable",
            __METHOD__
        ));
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->getData()->getIterator();
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
     * @param $id
     * @return null|ModelInterface
     */
    public function getById($id)
    {
        $record = $this->getProxyForRead()->read($id);
        if ($record instanceof ModelInterface) {
            return $record;
        } else if (ArrayUtils::hasStringKeys($record)) {
            return $this->createModel($record);
        }
        return null;
    }

    /**
     * @param $fieldName
     * @param $value
     */
    public function findRecord($fieldName, $value)
    {

    }

    /**
     * @param array|null $record
     * @return array|mixed|null|\ZfcDataManager\Model\AbstractModel
     */
    public function createModel(array $record = null)
    {
        if (ArrayUtils::hasStringKeys($record)) {
            $model = $this->dataManager->createModel($this->getModel(), $record);
        } else {
            $model = $this->dataManager->createModel($this->getModel());
        }
        $model->setParentStore($this);
        return $model;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        if (!$this->totalCount) {
            $proxy = $this->getProxyForRead();
            $total = $proxy->getTotalCount();
            $this->totalCount = (int) $total;
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
     *
     */
    public function getPageRange()
    {
        $total = $this->getTotalCount();
        $pageSize = $this->getPageSize();
        if (is_int($pageSize) && $pageSize > 0) {
            return ceil($total/$pageSize);
        }
        return 0;
    }

    /**
     * @param $page
     * @return AbstractStore
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = (int) $page;
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param $pageNumber
     * @return AbstractStore
     */
    public function getPage($pageNumber)
    {
        $pageNumber = (int) $pageNumber;
        $pageRange = $this->getPageRange();
        $pageSize = $this->getPageSize();

        if ($pageNumber <= $pageRange) {
            $startIndex = floor($pageNumber * $pageSize);
            $this->setCurrentPage($pageNumber);
            $this->setStartIndex($startIndex);
            $this->load();
        }
        return $this;
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
            $this->proxy = $proxyManager->getProxy($this->proxy);
        }
        $this->proxy->setModel($this->getModel());
        $this->proxy->setMapping($this->getMapping());
        return $this->proxy;
    }

    /**
     * @throws Exception\UnsupportedOperationException
     * @return ReadableProxyInterface|FilterableProxyInterface|SortableProxyInterface
     */
    public function getProxyForRead()
    {
        /** @var $proxy ReadableProxyInterface|FilterableProxyInterface|SortableProxyInterface */
        $proxy = $this->getProxy();
        if (!$proxy instanceof ReadableProxyInterface) {
            throw new Exception\UnsupportedOperationException(sprintf(
                "This store has not been configured with a readable proxy"
            ));
        }

        // Proxies are re-used since some may take time to load
        // (for example: when connecting to sources other than databases)
        if ($proxy instanceof SortableProxyInterface) {
            $proxy->setSortBy($this->getSorters());
        }

        if ($proxy instanceof FilterableProxyInterface) {
            $proxy->setFilterBy($this->getFilters());
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
    public function getEventManager()
    {
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