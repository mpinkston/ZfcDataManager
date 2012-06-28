<?php

namespace ZfcDataManager;

interface DataManagerAwareInterface
{
    /**
     * @abstract
     * @param DataManager $dataManager
     * @return mixed
     */
    public function setDataManager(DataManager $dataManager);
}