<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/**
 * customers defined options
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Button;

class Validation extends Widget
{
    /**
     * @return Widget
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            Button::class,
            ['label' => __('Add New Option'), 'class' => 'add', 'id' => 'add_new_defined_option']
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * @return string
     */
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
}
