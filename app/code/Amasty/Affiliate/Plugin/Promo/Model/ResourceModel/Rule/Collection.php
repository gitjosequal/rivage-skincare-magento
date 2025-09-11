<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Plugin\Promo\Model\ResourceModel\Rule;

use Amasty\Affiliate\Model\Rule\AffiliateQuoteResolver;
use Magento\Framework\DB\Select;

class Collection
{
    /**
     * @var bool
     */
    protected $processing = false;

    /**
     * @var AffiliateQuoteResolver
     */
    private $affiliateQuoteResolver;

    public function __construct(
        AffiliateQuoteResolver $affiliateQuoteResolver
    ) {
        $this->affiliateQuoteResolver = $affiliateQuoteResolver;
    }

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     */
    public function beforeLoad(
        \Magento\SalesRule\Model\ResourceModel\Rule\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($subject->isLoaded() || $this->processing) {
            return;
        }

        $affiliateRuleIds = $this->affiliateQuoteResolver->resolveRuleIds();
        if (empty($affiliateRuleIds)) {
            return;
        }

        $this->processing = true;

        $select = $subject->getSelect();
        $whereParts = $select->getPart(Select::WHERE);

        $affiliateRuleIds = implode("','", $affiliateRuleIds);
        foreach ($whereParts as $key => $wherePart) {
            if ($wherePart === "AND (`main_table`.`coupon_type` = '1')"
                || $wherePart === 'AND (main_table.coupon_type = 1)'
            ) {
                $whereParts[$key] = "AND ((`main_table`.`coupon_type` = '1')
                    OR main_table.rule_id IN ('{$affiliateRuleIds}'))";
                break;
            }
        }

        $select->setPart(Select::WHERE, $whereParts);
        $this->processing = false;
    }
}
