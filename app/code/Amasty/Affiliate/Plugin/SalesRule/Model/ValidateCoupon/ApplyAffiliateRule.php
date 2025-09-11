<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Plugin\SalesRule\Model\ValidateCoupon;

use Amasty\Affiliate\Model\Rule\AffiliateQuoteResolver;
use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\ValidateCoupon;

class ApplyAffiliateRule
{
    /**
     * @var AffiliateQuoteResolver
     */
    private $affiliateResolver;

    public function __construct(AffiliateQuoteResolver $affiliateResolver)
    {
        $this->affiliateResolver = $affiliateResolver;
    }

    /**
     * Suppress coupon code validation for Affiliate Sales Rules.
     *
     * Affiliate Sales Rules are with coupon code
     * Affiliate code can be stored in cookie.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        ValidateCoupon $subject,
        bool $result,
        Rule $rule,
        Address $address,
        ?string $couponCode = null
    ): bool {
        if ($result === false) {
            $affiliateRuleIds = $this->affiliateResolver->resolveRuleIds();
            if (in_array($rule->getId(), $affiliateRuleIds, false)) {
                $rule->setIsValidForAddress($address, true);

                return true;
            }
        }

        return $result;
    }
}
