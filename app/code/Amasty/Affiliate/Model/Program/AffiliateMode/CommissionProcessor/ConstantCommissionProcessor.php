<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Program\AffiliateMode\CommissionProcessor;

use Amasty\Affiliate\Api\Data\AccountInterface;
use Amasty\Affiliate\Api\Data\ProgramInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ConstantCommissionProcessor implements CommissionProcessorInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function validateProgram(ProgramInterface $program, AccountInterface $account): bool
    {
        if (!empty($program->getAvailableGroups()) || !empty($program->getAvailableCustomers())) {
            $customer = $this->customerRepository->getById($account->getCustomerId());
            if (!in_array($customer->getGroupId(), explode(',', $program->getAvailableGroups()))
                && !in_array($customer->getId(), explode(',', $program->getAvailableCustomers()))) {
                return false;
            }
        }

        return true;
    }

    public function getCommissionValue(ProgramInterface $program, int $affiliateAccountId): float
    {
        return (float)$program->getCommissionValue();
    }

    public function getCommissionValueType(ProgramInterface $program, int $affiliateAccountId): string
    {
        return (string)$program->getCommissionValueType();
    }

    public function getCommissionValueSecond(ProgramInterface $program, int $affiliateAccountId): float
    {
        return (float)$program->getCommissionValueSecond();
    }

    public function getCommissionTypeSecond(ProgramInterface $program, int $affiliateAccountId): string
    {
        return (string)$program->getCommissionTypeSecond();
    }

    public function isDifferentCommissionFromSecondOrder(ProgramInterface $program, int $affiliateAccountId): bool
    {
        return (bool)$program->getFromSecondOrder();
    }
}
