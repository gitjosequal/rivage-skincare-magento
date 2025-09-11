<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Block\Check;

class Banned extends Rules
{
    /**
     * @var \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     */
    private $banHelper;

    /**
     * Banned constructor.
     * @param \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->banHelper = $banHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->banHelper->customerHasData()
            ? $this->isAllowedToWriteReview()
            : true;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->isValid() ? '' : parent::toHtml();
    }

    /**
     * @return bool
     */
    public function isAllowedToWriteReview()
    {
        $customer = $this->banHelper->getCustomerModel();
        return !$this->banHelper->isCustomerBanned($customer);
    }
}
