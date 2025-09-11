<?php


namespace Rivaz\Expertadvice\Model\Resource;

class Items extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rivaz_expertadvice_items', 'id');
    }
}
