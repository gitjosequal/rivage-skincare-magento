<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var \Amasty\Rewards\Model\Rewards
     */
    protected $_rewardsModel;

    /**
     * CheckoutSubmitAllAfter constructor.
     *
     * @param \Amasty\Rewards\Model\Rewards $rewardsModel
     */
    public function __construct(
        \Amasty\Rewards\Model\Rewards $rewardsModel
    ) {
        $this->_rewardsModel = $rewardsModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Quote\Model\Quote $quote
         */
        $quote = $observer->getEvent()->getQuote();

        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $observer->getOrder();

        if ($quote->getAmrewardsPoint()) {
            $this->_rewardsModel->addPoints(
                -$quote->getAmrewardsPoint(),
                __('Order Paid'),
                $quote->getCustomerId(),
                __('Order %1 paid', $order->getId())
            );
        }
    }
}
