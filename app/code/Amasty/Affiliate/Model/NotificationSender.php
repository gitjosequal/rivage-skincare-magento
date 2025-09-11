<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model;

use Amasty\Affiliate\Api\Data\AccountInterface;
use Amasty\Affiliate\Model\Source\Status;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;

class NotificationSender
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Mailsender
     */
    private $mailsender;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Status
     */
    private $statusOptions;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Mailsender $mailsender,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Status $statusOptions = null // TODO mode to not optional
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mailsender = $mailsender;
        $this->customerRepository = $customerRepository;
        // OM for backward compatibility
        $this->statusOptions = $statusOptions ?? ObjectManager::getInstance()->get(Status::class);
    }

    /**
     * @param AccountInterface $account
     * @param Int $status
     */
    public function sendAffiliateStatusEmail(AccountInterface $account, $status)
    {
        if ($this->scopeConfig->getValue('amasty_affiliate/email/affiliate/account_status')
            && $account->getReceiveNotifications()
        ) {
            $emailData = $account->getData();
            $emailData['name'] = $account->getFirstname() . ' ' . $account->getLastname();
            $emailData['status'] = $this->getStatusText($status);
            $customer = $this->customerRepository->getById($account->getCustomerId());
            $sendToMail = $customer->getEmail();
            $this->mailsender->sendAffiliateMail($emailData, Mailsender::TYPE_AFFILIATE_STATUS, $sendToMail, $account);
        }
    }

    public function getStatusText($status): string
    {
        $optionArray = $this->statusOptions->toArray();
        $status = $optionArray[$status] ?? __('Inactive');

        return $status->render();
    }

    /**
     * Send email notification to admin about new affiliate account
     *
     * @param \Amasty\Affiliate\Model\Account $account
     */
    public function sendAdminNotification($account)
    {
        if (!$this->scopeConfig->getValue('amasty_affiliate/email/admin/new_affiliate')) {
            return;
        }
        $customer = $this->customerRepository->getById($account->getCustomerId());
        $emailData = $account->getData();
        $emailData['name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        $emailData['email'] = $customer->getEmail();
        $sendToMail = $this->scopeConfig->getValue('amasty_affiliate/email/general/recipient_email');

        $this->mailsender->sendMail($emailData, Mailsender::TYPE_ADMIN_NEW_ACCOUNT, $sendToMail);
    }

    /**
     * Send email notification to affiliate about creating of account
     *
     * @param \Amasty\Affiliate\Model\Account $account
     */
    public function sendAffiliateNotification(AccountInterface $account): void
    {
        if (!$this->scopeConfig->getValue('amasty_affiliate/email/affiliate/welcome')) {
            return;
        }
        $emailData = $account->getData();
        $emailData['name'] = $account->getFirstname() . ' ' . $account->getLastname();
        $customer = $this->customerRepository->getById($account->getCustomerId());
        $sendToMail = $customer->getEmail();

        $this->mailsender->sendAffiliateMail($emailData, Mailsender::TYPE_AFFILIATE_WELCOME, $sendToMail, $account);
    }
}
