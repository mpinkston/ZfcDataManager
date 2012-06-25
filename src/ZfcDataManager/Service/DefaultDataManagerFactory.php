<?php

namespace ZfcDataManager\Service;

class DefaultDataManagerFactory extends AbstractDataManagerFactory
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'default';
    }
}