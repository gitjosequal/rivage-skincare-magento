<?php

namespace Rivaz\Expertadvice\Controller\Adminhtml\Items;

class NewAction extends \Rivaz\Expertadvice\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
