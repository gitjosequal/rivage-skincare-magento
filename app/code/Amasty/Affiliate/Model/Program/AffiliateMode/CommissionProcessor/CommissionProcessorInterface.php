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

interface CommissionProcessorInterface
{
    public function validateProgram(ProgramInterface $program, AccountInterface $account): bool;

    public function getCommissionValue(ProgramInterface $program, int $affiliateAccountId): float;

    public function getCommissionValueType(ProgramInterface $program, int $affiliateAccountId): string;

    public function getCommissionValueSecond(ProgramInterface $program, int $affiliateAccountId): float;

    public function getCommissionTypeSecond(ProgramInterface $program, int $affiliateAccountId): string;

    public function isDifferentCommissionFromSecondOrder(ProgramInterface $program, int $affiliateAccountId): bool;
}
