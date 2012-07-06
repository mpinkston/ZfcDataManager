<?php

namespace ZfcDataManager\View\Helper;

class GravityPager extends AbstractPager
{
    /**
     * The 'mass' of the current page. Defines how tightly grouped
     * neighboring pages should appear. (neighboring pages all have a mass of 1)
     *
     */
    protected $mass = 2;

    /**
     * @return array
     */
    public function getPageList()
    {
        if (isset($this->config['gravity'])) {
            $this->gravity = $this->config['gravity'];
        }

        $pageList = array();

        $totalPages = $this->store->getPageRange();
        $listSize = ($totalPages >= $this->listSize)?$this->listSize:$totalPages;
        $currentPage = $this->store->getCurrentPage();


        for ($x=1; $x<$listSize; $x++) {

/*
            $point = ceil($totalPages/$listSize) * $x;

            $distance = round(abs(($currentPage - $point)/$totalPages), 2); // distance (r)
            if ($distance){
                $force = $this->mass/pow($distance, 2);
            } else {
                $force = 0;
            }



            $pageList[] = "{$distance} : {$force}";
*/
        }

        return $pageList;
    }
}