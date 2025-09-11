<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */
namespace Amasty\Rewards\Plugin\SalesRule\Model\Quote;

class Discount
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Amasty\Rewards\Model\Rewards
     */
    protected $rewards;

    /**
     * @var \Amasty\Rewards\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $validator;

    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Rewards\Model\Rewards $rewards,
        \Amasty\Rewards\Model\Rule $rule,
        \Amasty\Rewards\Helper\Data $helper,
        \Magento\SalesRule\Model\Validator $validator
    ) {
        $this->registry  = $registry;
        $this->rewards   = $rewards;
        $this->rule      = $rule;
        $this->validator = $validator;
        $this->helper    = $helper;
    }

    public function aroundCollect(
        \Magento\SalesRule\Model\Quote\Discount $subject,
        \Closure $closure,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        /** @var $address \Magento\Quote\Model\Quote\Address */
        $address = $shippingAssignment->getShipping()->getAddress();

        $result = $closure($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();

        if (!$items || !$quote->hasAmrewardsPoint()) {
            return $result;
        }

        $appliedPoints = $this->rewards->calculateDiscount($items, $total, $quote->getAmrewardsPoint());
        $appliedPoints = $this->helper->roundPoints($appliedPoints);

        $currentUsedPoints = $this->registry->registry('ampoints_used');

        if ($appliedPoints > 0 && !$currentUsedPoints) {
            if ($appliedPoints != $quote->getAmrewardsPoint()) {
                $quote->setData('amrewards_point', $appliedPoints);
            }
            $this->registry->register('ampoints_used', $appliedPoints);
        }

        $this->rule->addDiscountDescription($address, $appliedPoints);
        $this->validator->prepareDescription($address);

        $total->setDiscountDescription($address->getDiscountDescription());

        $total->setSubtotalWithDiscount($total->getSubtotal() + $total->getDiscountAmount());
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $total->getBaseDiscountAmount());

        return $result;
    }
}
