<?php

namespace ZfcDataManager\View\Helper;

class SlidingPager extends AbstractPager
{
    /**
     * @return array
     */
    public function getPageList()
    {
        $totalPages = $this->store->getPageRange();
        $listSize = ($totalPages >= $this->listSize)?$this->listSize:$totalPages;
        $currentPage = $this->store->getCurrentPage();

        $start = ($currentPage - ($listSize/2));
        if ($start < 1) {
            $start = 1;
        } else if ($start + ($listSize/2) > $totalPages) {
            $start = $totalPages - $listSize;
        }

        $pageList = array(1, $totalPages);
        for ($x = $start; $x <= $listSize+$start; $x++) {
            if (!in_array($x, $pageList)) {
                $pageList[] = $x;
            }
        }
        sort($pageList);

        return $pageList;
    }
}