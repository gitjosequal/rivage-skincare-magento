<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model;

use Amasty\Affiliate\Model\Repository\AccountRepository;
use Magento\Customer\Model\Session;

class CurrentAccount
{
    /**
     * @var int
     */
    private $accountId;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        Session $customerSession,
        AccountRepository $accountRepository
    ) {
        $this->customerSession = $customerSession;
        $this->accountRepository = $accountRepository;
    }

    public function getAccountId(): int
    {
        if (!$this->accountId) {
            $customerId = $this->customerSession->getCustomerId();
            $account = $this->accountRepository->getByCustomerId($customerId);
            $this->accountId = (int)$account->getAccountId();
        }

        return $this->accountId;
    }
}
