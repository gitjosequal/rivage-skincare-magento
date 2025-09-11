<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManager;
use Magento\Store\Model\StoreManagerInterface;

class RulesHelper extends AbstractHelper
{
    const XML_PATH_MAGEWORKSHOP_CUSTOMER_PERMISSIONS_GENERAL_ENABLED = 'mageworkshop_detailedreview/mageworkshop_customerpermissions/enabled';
    const XML_PATH_MAGEWORKSHOP_CUSTOMER_PERMISSIONS_GENERAL_ENABLED_AUTO_APPROVE = 'mageworkshop_detailedreview/mageworkshop_customerpermissions/enabled_auto_approve';

    /** @var Session $customerSession */
    protected $customerSession;

    /** @var SessionManager $sessionManager */
    protected $sessionManager;

    /** @var CustomerRepositoryInterface $customerRepositoryInterface */
    protected $customerRepositoryInterface;

    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var CustomerFactory $customerFactory */
    protected $customerFactory;

    /** @var StoreManagerInterface $scopeConfig */
    protected $storeManagerInterface;

    /**
     * RulesHelper constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerFactory $customerFactory
     * @param Context $context
     * @param Session $customerSession
     * @param SessionManager $sessionManager
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerFactory $customerFactory,
        Context $context,
        Session $customerSession,
        SessionManager $sessionManager,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->searchCriteriaBuilder       = $searchCriteriaBuilder;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory             = $customerFactory;
        $this->customerSession             = $customerSession;
        $this->sessionManager              = $sessionManager;
        $this->storeManagerInterface       = $storeManagerInterface;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        if ($this->customerSession->isLoggedIn()) {
            $email = (string) $this->customerSession->getCustomer()->getEmail();
        } else {
            $email = (string) $this->sessionManager->getData('customer_email') ?: '';
        }

        return $email;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerModel()
    {
        if (!$this->customerHasData() || $this->isCustomerLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
        } else {
            $websiteId = $this->storeManagerInterface->getWebsite()->getId();
            $customer = $this->customerFactory->create(['data' => ['website_id' => $websiteId]])->loadByEmail($this->sessionManager->getData('customer_email'));
        }
        return $customer;
    }

    /**
     * @return bool
     */
    protected function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return bool
     */
    public function customerHasData()
    {
        return ($this->isCustomerLoggedIn() || $this->sessionManager->getData('customer_email'));
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_CUSTOMER_PERMISSIONS_GENERAL_ENABLED);
    }

    public function isAutoApproveEnabled()
    {
        return ($this->isModuleEnabled() && $this->scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_CUSTOMER_PERMISSIONS_GENERAL_ENABLED_AUTO_APPROVE));
    }
}
