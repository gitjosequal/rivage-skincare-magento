<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Plugin\CustomerData;

/**
 * Customer Plugin
 */
class Customer
{

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor.
     *
     * @param \Magento\Authorization\Model\UserContextInterface $userContext User context instance
     * @param \Magento\Customer\Model\Session $customerSession Customer session instance
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->userContext = $userContext;
        $this->customerSession = $customerSession;
    }

    /**
     * Plugin to get the customer data.
     *
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param array $result
     * @return array Modified section data
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $result['customer_id'] = (int) $this->userContext->getUserId();
        $result['customer_group_id'] = (int) $this->customerSession->getCustomer()->getGroupId();

        return $result;
    }
}
