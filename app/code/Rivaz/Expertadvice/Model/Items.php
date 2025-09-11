<?php

namespace Rivaz\Expertadvice\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Rivaz\Expertadvice\Model\Resource\Items');
    }
}
