<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class CartUpdateAfter implements ObserverInterface
{
    /**
     * @var \Rivage\GtmExtension\Helper\Data
     */
    protected $helper;

    /**
     * @var \Rivage\GtmExtension\Block\Datalayer
     */
    protected $datablock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Rivage\GtmExtension\Block\Datalayer $datablock
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper,
        \Rivage\GtmExtension\Block\Datalayer $datablock,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->datablock = $datablock;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Execute function to process cart item changes and update GtmExtension data in session.
     *
     * @param \Magento\Framework\Event\Observer $observer Observer instance
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        // Get the items in the cart's quote
        $quoteItems = $observer->getCart()->getQuote()->getItemsCollection();

        // Loop through each item in the cart
        foreach ($quoteItems as $item) {

            // Calculate the quantity change for the item
            $qtyAfterChange = $item->getQty() - $item->getQtyBeforeChange();
            // @codingStandardsIgnoreStart
            if ($qtyAfterChange != 0) {
                if ($qtyAfterChange > 0) {
                     // Handle item addition to cart
                    $addToCartJsonData = $this->datablock->getAddtocartJsonData($qtyAfterChange, $item->getProduct(), $item->getBuyRequest()->getData(), true);
                    $this->updateSessionData('GA4AddToCartData', $addToCartJsonData);
                } else {
                    // Handle item removal from cart
                    $removeFromCartJsonData = $this->datablock->getRemovefromcartJsonData(abs($qtyAfterChange), $item->getProduct(), $item);
                    $this->updateSessionData('GA4RemoveFromCartData', $removeFromCartJsonData);
                }
            }
            // @codingStandardsIgnoreEnd
        }

        return $this;
    }

    /**
     * Update session data with combined GtmExtension JSON data.
     *
     * @param string $key Session data key
     * @param mixed $data New GtmExtension JSON data to be added
     * @return void
     */
    private function updateSessionData($key, $data)
    {
        $currentData = $this->checkoutSession->getData($key);
        $newData = $this->datablock->combineAddtocartJsonData($currentData, $data);
        $this->checkoutSession->setData($key, $newData);
    }
}
