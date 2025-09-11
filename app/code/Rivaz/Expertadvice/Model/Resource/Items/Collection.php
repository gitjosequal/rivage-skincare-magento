<?php

namespace Rivaz\Expertadvice\Model\Resource\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Rivaz\Expertadvice\Model\Items', 'Rivaz\Expertadvice\Model\Resource\Items');
    }
}
