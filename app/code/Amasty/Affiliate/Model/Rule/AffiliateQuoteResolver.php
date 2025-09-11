<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Rule;

use Amasty\Affiliate\Api\AccountRepositoryInterface;
use Amasty\Affiliate\Api\Data\AccountInterface;
use Amasty\Affiliate\Model\RegistryConstants;
use Amasty\Affiliate\Model\ResourceModel\Program\CollectionFactory;
use Amasty\Affiliate\Model\Source\Status;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Model\QuoteRepository;

/**
 * @since 2.3.0 crass created by Plugin refactoring
 */
class AffiliateQuoteResolver
{
    /**
     * @var array|null
     */
    private $resolvedRuleIds = null;

    /**
     * @var CollectionFactory
     */
    private $programCollectionFactory;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var State
     */
    private $scopeManager;

    public function __construct(
        CollectionFactory $programCollectionFactory,
        CookieManagerInterface $cookieManager,
        AccountRepositoryInterface $accountRepository,
        CheckoutSession $checkoutSession,
        QuoteRepository $quoteRepository,
        CustomerRepositoryInterface $customerRepository,
        State $scopeManager
    ) {
        $this->programCollectionFactory = $programCollectionFactory;
        $this->cookieManager = $cookieManager;
        $this->accountRepository = $accountRepository;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->customerRepository = $customerRepository;
        $this->scopeManager = $scopeManager;
    }

    /**
     * Resolve applicable affiliate rule ids
     */
    public function resolveRuleIds(): array
    {
        if ($this->resolvedRuleIds === null) {
            $this->resolvedRuleIds = [];
            $account = $this->resolveAffiliateAccount();
            if (!$account) {
                return [];
            }

            $customer = $this->customerRepository->getById($account->getCustomerId());
            /* Do not apply affiliate link to the same customer */
            if ($quoteId = $this->checkoutSession->getQuoteId()) {
                $quote = $this->quoteRepository->get($quoteId);
                if ($customer->getEmail() === $quote->getCustomerEmail()) {
                    return [];
                }
            }

            $this->resolvedRuleIds = $this->programCollectionFactory
                ->create()
                ->addActiveFilter()
                ->addCustomerAndGroupFilter($customer->getId(), $customer->getGroupId())
                ->addOrderCounterFilter((int)$account->getAccountId())
                ->getColumnValues('rule_id');
        }

        return $this->resolvedRuleIds;
    }

    public function resolveAffiliateAccount(): ?AccountInterface
    {
        $couponCode = null;
        if ($this->checkoutSession->getQuoteId()) {
            $quote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());
            $couponCode = $quote->getCouponCode();
        }
        if (!empty($couponCode)) {
            try {
                $account = $this->accountRepository->getByCouponCode($couponCode);
                if ($account->getStatus() === Status::ENABLED) {
                    return $account;
                }
            } catch (NoSuchEntityException $e) {
                return null;
            }

            return null;
        }
        $affiliateCode = $this->cookieManager
            ->getCookie(RegistryConstants::CURRENT_AFFILIATE_ACCOUNT_CODE);
        if ($affiliateCode !== null && $this->scopeManager->getAreaCode() !== Area::AREA_ADMINHTML) {
            try {
                $account = $this->accountRepository->getByReferringCode($affiliateCode);
            } catch (NoSuchEntityException $e) {
                return null;
            }
            if ($account->getStatus() === Status::ENABLED) {
                return $account;
            }

        }

        return null;
    }
}
