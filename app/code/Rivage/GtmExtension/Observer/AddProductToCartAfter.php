<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddProductToCartAfter implements ObserverInterface
{
    /**
     * @var \Rivage\GtmExtension\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Observer For AddToCartAfter Event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        $item = $observer->getQuoteItem();
        $itemPrice = $item->getPrice();
        $this->checkoutSession->setGA4LastProductPrice($itemPrice);

        return $this;
    }
}
