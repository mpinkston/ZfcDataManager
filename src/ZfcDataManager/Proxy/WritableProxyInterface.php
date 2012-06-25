<?php

namespace ZfcDataManager\Proxy;

interface WritableProxyInterface extends ProxyInterface
{
    /**
     * @abstract
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @abstract
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * @abstract
     * @param $id
     * @return mixed
     */
    public function delete($id);
}