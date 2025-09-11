<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Block\Adminhtml\Rule\Grid\Renderer;

class Groups extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    public function render(\Magento\Framework\DataObject $row)
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $hlp = $om->get('Amasty\Rewards\Helper\Data');

        $groups = $row->getData('cust_groups');
        if (!$groups) {
            return __('Restricts For All');
        }
        $groups = explode(',', $groups);

        $html = '';
        foreach($hlp->getAllGroups() as $row)
        {
            if (in_array($row['value'], $groups)){
                $html .= $row['label'] . "<br />";
            }
        }
        return $html;
    }

}