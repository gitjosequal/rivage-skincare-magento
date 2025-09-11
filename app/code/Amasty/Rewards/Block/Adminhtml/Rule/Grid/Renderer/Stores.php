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

use Magento\Framework\DataObject;

class Stores extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    public function render(DataObject $row)
    {
        $stores = $row->getData('stores');
        if (!$stores) {
            return __('Restricts in All');
        }

        $html = '';
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $data = $om->get('Magento\Store\Model\System\Store')->getStoresStructure(false, explode(',', $stores));
        foreach ($data as $website) {
            $html .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $html .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $store) {
                    $html .= str_repeat('&nbsp;', 6) . $store['label'] . '<br/>';
                }
            }
        }
        return $html;
    }

}