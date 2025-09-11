<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model;

use Amasty\Affiliate\Api\Data\AccountInterfaceFactory;
use Amasty\Affiliate\Model\Repository\AccountRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AccountCreator
{

    /**
     * @var AccountInterfaceFactory
     */
    private $accountFactory;
    /**
     * @var RefferingCodesManagement
     */
    private $refferingCodesManagement;
    /**
     * @var Repository\AccountRepository
     */
    private $accountRepository;
    /**
     * @var NotificationSender
     */
    private $notificationSender;
    /**
     * @var CouponCreator
     */
    private $couponCreator;

    public function __construct(
        AccountInterfaceFactory $accountFactory,
        RefferingCodesManagement $refferingCodesManagement,
        AccountRepository $accountRepository,
        NotificationSender $notificationSender,
        CouponCreator $couponCreator,
        ?ScopeConfigInterface $scopeConfig = null // TODO delete
    ) {
        $this->accountFactory = $accountFactory;
        $this->refferingCodesManagement = $refferingCodesManagement;
        $this->accountRepository = $accountRepository;
        $this->notificationSender = $notificationSender;
        $this->couponCreator = $couponCreator;
    }

    /**
     * @param int $customerId
     * @param array $data
     * @return \Amasty\Affiliate\Api\Data\AccountInterface
     */
    public function createAccount($customerId, $data)
    {
        $account = $this->accountFactory->create();
        $account->addData($data);
        $account->setCustomerId($customerId);
        $account->setAcceptedTermsConditions(true);
        $account->setReferringCode($this->refferingCodesManagement->generateReferringCode());
        $this->accountRepository->save($account);

        $this->couponCreator->addCoupon($account->getAccountId());
        $this->notificationSender->sendAdminNotification($account);
        $this->notificationSender->sendAffiliateNotification($account);

        return $account;
    }
}
