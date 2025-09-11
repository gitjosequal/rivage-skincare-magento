<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Form extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_form';
        $this->_blockGroup = \MageWorkshop\DetailedReview\Model\Module\DetailsData::MODULE_CODE;
        $this->_headerText = __('Review Forms');
        $this->_addButtonLabel = __('Add New Review Form');
        parent::_construct();
    }
}
