<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Plugin\AdvancedSalesRules\Model\ResourceModel\Rule\Condition\Filter;

use Amasty\Affiliate\Model\Rule\AffiliateQuoteResolver;
use Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter;

/**
 * Compatibility with EE
 */
class AddAffiliateRuleIds
{
    /**
     * @var AffiliateQuoteResolver
     */
    private $affiliateQuoteResolver;

    public function __construct(AffiliateQuoteResolver $affiliateQuoteResolver)
    {
        $this->affiliateQuoteResolver = $affiliateQuoteResolver;
    }

    /**
     * @param Filter $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFilterRules(Filter $subject, array $result): array
    {
        $ruleIds = $this->affiliateQuoteResolver->resolveRuleIds();
        if (!empty($ruleIds)) {
            $result = array_merge($result, $ruleIds);
        }

        return $result;
    }
}
