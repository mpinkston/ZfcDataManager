<?php

namespace ZfcDataManager\Proxy\Db;

use \Traversable;
use ZfcDataManager\Proxy\FilterableProxyInterface;
use ZfcDataManager\Proxy\SortableProxyInterface;
use ZfcDataManager\Proxy\WritableProxyInterface;
use ZfcDataManager\Proxy\AbstractProxy;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\ArrayUtils;

class TableGatewayProxy extends AbstractProxy
    implements FilterableProxyInterface, SortableProxyInterface, WritableProxyInterface
{
    /**
     * @var string|Adapter
     */
    public $adapter;

    /**
     * @var array
     */
    public $filters;

    /**
     * @var array
     */
    public $sorters;

    /**
     * @var TableGateway
     */
    protected $gateway;

    /**
     * @param $adapter
     * @return TableGatewayProxy
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        if (!($this->adapter instanceof Adapter)) {
            if (is_string($this->adapter)) {
                $serviceLocator = $this->dataManager->getServiceLocator();
                $this->adapter = $serviceLocator->get($this->adapter);
            }
            // @TODO: throw an exception or something?
        }
        return $this->adapter;
    }

    /**
     * @return TableGateway
     */
    public function getTableGateway()
    {
        if (!$this->gateway) {
            $this->gateway = new TableGateway(
                $this->mapping,
                $this->getAdapter()
            );
        }
        return $this->gateway;
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function setFilterBy($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @param $sorters
     * @return mixed
     */
    public function setSortBy($sorters)
    {
        $this->sorters = $sorters;
        return $this;
    }

    /**
     * @param int $startIndex
     * @param null $pageSize
     * @return mixed
     */
    public function fetch($startIndex = 0, $pageSize = null)
    {
        $gateway = $this->getTableGateway();
        $gateway->initialize();

        /** @var $sql \Zend\Db\Sql\Sql */
        $sql = $gateway->getSql();
        $select = $sql->select();

        // @TODO: check for filters/sorters etc.

        $select->offset($startIndex);
        if (is_int($pageSize)) {
            $select->limit($pageSize);
        }

        /** @var $result \Zend\Db\ResultSet\ResultSetInterface */
        $result = $gateway->selectWith($select);
        if ($result instanceof Traversable) {
            $result = ArrayUtils::iteratorToArray($result);
        }
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        // TODO: Implement read() method.
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        // TODO: Implement getTotalCount() method.
    }
}