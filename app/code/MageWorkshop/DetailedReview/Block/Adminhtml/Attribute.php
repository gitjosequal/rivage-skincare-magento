<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml;

class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = \MageWorkshop\DetailedReview\Model\Module\DetailsData::MODULE_CODE;
        $this->_headerText = __('Review Fields');
        $this->_addButtonLabel = __('New Review Field');
        parent::_construct();
    }
}
