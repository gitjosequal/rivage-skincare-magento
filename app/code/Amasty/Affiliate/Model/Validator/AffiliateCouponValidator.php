<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Validator;

use Amasty\Affiliate\Api\AccountRepositoryInterface;
use Amasty\Affiliate\Model\ResourceModel\Program\CollectionFactory;
use Amasty\Affiliate\Model\Source\Status;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;

class AffiliateCouponValidator
{
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CollectionFactory
     */
    private $programCollectionFactory;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var array
     */
    private $cache = [];

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        CustomerRepositoryInterface $customerRepository,
        CollectionFactory $programCollectionFactory,
        CheckoutSession $checkoutSession = null, // TODO not optional
        QuoteRepository $quoteRepository = null // TODO not optional
    ) {
        $this->accountRepository = $accountRepository;
        $this->customerRepository = $customerRepository;
        $this->programCollectionFactory = $programCollectionFactory;
        $this->checkoutSession = $checkoutSession ?? ObjectManager::getInstance()->get(CheckoutSession::class);
        $this->quoteRepository = $quoteRepository ?? ObjectManager::getInstance()->get(QuoteRepository::class);
    }

    /**
     * @param string $couponCode
     * @return bool
     */
    public function validate($couponCode)
    {
        if (!array_key_exists($couponCode, $this->cache)) {
            $this->cache[$couponCode] = $this->getValidationResult($couponCode);
        }

        return $this->cache[$couponCode];
    }

    /**
     * @param string $couponCode
     * @return bool
     */
    private function getValidationResult($couponCode)
    {
        try {
            $account = $this->accountRepository->getByCouponCode($couponCode);
            if ($account->getStatus() !== Status::ENABLED) {
                return false;
            }
        } catch (NoSuchEntityException $e) {
            return true;
        }

        try {
            $customer = $this->customerRepository->getById($account->getCustomerId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        /* Do not apply affiliate code to the same customer */
        if ($quoteId = $this->checkoutSession->getQuoteId()) {
            $quote = $this->quoteRepository->get($quoteId);
            if ($customer->getEmail() === $quote->getCustomerEmail()) {
                return false;
            }
        }

        $collection = $this->programCollectionFactory->create();
        $collection->addCouponFilter($couponCode);
        $collection->addCustomerAndGroupFilter(
            $customer->getId(),
            $customer->getGroupId()
        );
        $collection->addOrderCounterFilter((int)$account->getAccountId());
        $collection->addActiveFilter();

        return $collection->getSize() > 0;
    }
}
