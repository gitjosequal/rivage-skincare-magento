<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Controller\Adminhtml\Rewards;

class Grid extends \Amasty\Rewards\Controller\Adminhtml\Rewards
{
    public function execute()
    {
        $this->initCurrentCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
