<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteLoadAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    protected $_rewardsQuote;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\Rewards\Model\ResourceModel\Quote $quote
    ) {
        $this->_rewardsQuote = $quote;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        $rewards = $this->_rewardsQuote->loadByQuoteId($quote->getId());

        if (isset($rewards['reward_points'])) {
            $quote->setData(
                'amrewards_point',
                $rewards['reward_points']
            );
        }
    }
}
