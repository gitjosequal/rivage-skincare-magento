<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Helper;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Review\Model\Review;
use Magento\Store\Model\StoreManagerInterface;

class BanHelper extends RulesHelper
{
    const BAN_FOR_30_DAYS  = 30;
    const BAN_FOR_90_DAYS  = 90;
    const BAN_FOR_180_DAYS = 180;
    const BAN_FOR_360_DAYS = 360;

    /** @var DateTime $dateTime */
    protected $dateTime;

    /** @var ManagerInterface $messageManager */
    protected $messageManager;

    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /** @var CustomerFactory $customerFactory */
    protected $customerFactory;

    /**.
     * BanHelper constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerFactory $customerFactory
     * @param Context $context
     * @param Session $customerSession
     * @param SessionManager $sessionManager
     * @param StoreManagerInterface $storeManagerInterface
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param DateTime $dateTime
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerFactory $customerFactory,
        Context $context,
        Session $customerSession,
        SessionManager $sessionManager,
        StoreManagerInterface $storeManagerInterface,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        DateTime $dateTime
    ) {
        $this->collectionFactory = $collectionFactory;

        $this->messageManager    = $messageManager;
        $this->dateTime          = $dateTime;
        parent::__construct(
            $customerRepositoryInterface,
            $searchCriteriaBuilder,
            $customerFactory,
            $context,
            $customerSession,
            $sessionManager,
            $storeManagerInterface
        );
    }

    /**
     * @return array
     */
    public function getBanPeriodsOptionArray()
    {
        $result = [];
        foreach ($this->getBanPeriods() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }

    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getBanPeriods()
    {
        return [
            self::BAN_FOR_30_DAYS  => __('30 Days'),
            self::BAN_FOR_90_DAYS  => __('90 Days'),
            self::BAN_FOR_180_DAYS => __('180 Days'),
            self::BAN_FOR_360_DAYS => __('360 Days'),
        ];
    }

    /**
     * @param AbstractCollection $reviewCollection
     * @param $period
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function banByReviews(AbstractCollection $reviewCollection, $period)
    {
        $customerIds        = [];
        $cantBan            = [];
        $successfullyBanned = [];
        /** @var \Magento\Review\Model\Review $review */
        foreach ($reviewCollection as $review) {
            if ($customerId = $review->getCustomerId()) {
                $customerIds[] = $customerId;
            }
        }

        $customerIds = array_unique($customerIds);
        $customersArray = $this->collectionFactory->create()
            ->addAttributeToFilter('entity_id', ['in' => $customerIds])
            ->getItems();

        foreach ($reviewCollection as $review) {
            if (!$review->getCustomerId()) {
                $cantBan[] = $review->getId();
                continue;
            }
            try {
                $successfullyBanned[] = $this->banCustomer($customersArray[$review->getCustomerId()], $period);
                $review->setData('status_id', Review::STATUS_NOT_APPROVED)->save();
            } catch (\Exception $e) {
                $cantBan[] = $review->getId();
            }
        }

        if ($successfullyBanned) {
            $this->messageManager->addSuccessMessage(
                __('Following author(s) was/were banned: %1', implode(', ', array_unique($successfullyBanned)))
            );
        }
        if ($cantBan) {
            $this->messageManager->addErrorMessage(
                __('Can\'t ban author(s) of following review(s): %1. Seems it was posted by anonymous user(s)', implode(', ', $cantBan))
            );
        }
        return $this;
    }

    /**
     * @param  $customer
     * @param $period
     * @return mixed
     */
    public function banCustomer($customer, $period)
    {
        $bannedTill = $this->dateTime->gmtDate(null, $period . 'days');

//        $dataModel = $customer->getDataModel();
//        $dataModel->setData('banned_till', $bannedTill);
//        $dataModel->setBannedTill($bannedTill);
//        return $this->customerRepositoryInterface->save($dataModel);

        // Need to use attribute set or future updates can cause data loss
        /** @var \Magento\Customer\Model\Backend\Customer $customer */
        if (!$customer->getAttributeSetId()) {
            $customer->setAttributeSetId(
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
            );
        }
        $customer->setData('banned_till', $bannedTill);
        $customer->save();
        return $customer->getName();
    }

    /**
     * @param $customerCollection
     * @return array
     */
    public function removeFromBanList($customerCollection)
    {
        $bannedCustomers = [];
        /** @var \Magento\Customer\Model\Backend\Customer $customer */
        foreach ($customerCollection as $customer) {

//            $dataModel = $customer->getDataModel();
//            $dataModel->setData('banned_till', '');
//            $this->customerRepositoryInterface->save($dataModel);

            $bannedCustomers[] = $customer->getName();
            if (!$customer->getAttributeSetId()) {
                $customer->setAttributeSetId(CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
            }
            $customer->setBannedTill('');
            $customer->save();
        }
        return $bannedCustomers;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return bool
     */
    public function isCustomerBanned($customer)
    {
        if (!$customer->getId() || !$customer->getBannedTill()) {
            return false;
        }
        $bannedTill = $this->dateTime->timestamp($customer->getBannedTill() . '+ 1 day');
        $now = $this->dateTime->timestamp();
        return ($bannedTill >= $now);
    }
}
