<?php

namespace ZfcDataManager\View\Helper;

use ZfcDataManager\Store\StoreInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * @TODO: figure out how you want pagers to render
 */
abstract class AbstractPager extends AbstractHelper
{
    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * @var array|null
     */
    protected $config;

    /**
     * Defines how many items should appear in the pager
     * @var int
     */
    protected $listSize = 8;

    /**
     * @abstract
     * @return array
     */
    abstract public function getPageList();

    /**
     * @param \ZfcDataManager\Store\StoreInterface $store
     * @param array $config
     * @return string
     */
    public function __invoke(StoreInterface $store, array $config = null)
    {
        $this->setStore($store);
        if ($config) {
            $this->setConfig($config);
        }

        return $this->render();
    }

    /**
     * @param \ZfcDataManager\Store\StoreInterface $store
     * @return AbstractPager
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @param array $config
     * @return AbstractPager
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return string
     */
    protected function render()
    {
        // @TODO: should I even attempt to render here, or use partials like the built-in paginator?
        // (or both/neither?)

        /** @var $htmlList \Zend\View\Helper\HtmlList */
        $list = $this->getPageList();

        $html = '<ul class="paginator">';
        foreach ($list as $page) {
            if ($page == $this->store->getCurrentPage()) {
                $html .= "  <li class=\"current page\">{$page}</li>";
            } else {
                $html .= "  <li class=\"page\">{$page}</li>";
            }
        }
        $html .= '</ul>';

        return $html;
    }
}