<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Controller\Check;

use Magento\Framework\Controller\ResultFactory;

class Rules extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Session\SessionManager $sessionManager
     */
    private $sessionManager;

    /**
     * @var \Magento\Catalog\Helper\Product $productHelper
     */
    private $productHelper;

    /**
     * Rules constructor.
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->sessionManager = $sessionManager;
        $this->productHelper = $productHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($productId = $this->getRequest()->getParam('productId')) {
            $this->productHelper->initProduct($productId, $this);
        }

        if ($email = $this->getRequest()->getParam('verify-email')) {
            $this->sessionManager->setData('customer_email', $email);
            // put some flag for the blocks
        }

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
