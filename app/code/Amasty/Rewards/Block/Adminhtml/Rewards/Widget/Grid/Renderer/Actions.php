<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Rewards\Widget\Grid\Renderer;

class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $action = $row->getData('action');

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $hlp = $om->get('Amasty\Rewards\Helper\Data');

        $actions = $hlp->getActions();

        if (isset($actions[$action])) {
            $result = $actions[$action];
        } else {
            $result = $action;
        }

        return $result;
    }

}