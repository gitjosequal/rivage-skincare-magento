<?php

namespace Rivaz\Expertadvice\Block\Adminhtml\Items\View;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rivaz_expertadvice_items_view_tabs');
        $this->setDestElementId('view_form');
        $this->setTitle(__('Item'));
    }
}
