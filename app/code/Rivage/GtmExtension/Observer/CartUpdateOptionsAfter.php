<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class CartUpdateOptionsAfter implements ObserverInterface
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
     * Captures the changes in cart item quantities and processes them for GtmExtension tracking.
     *
     * @param \Magento\Framework\Event\Observer $observer The event observer.
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        // Get the updated cart item data
        $item = $observer->getItem()->getData();

        if ($item->getQtyBeforeChange() != $item->getQty()) {
            $qtyChange =  $item->getQty() - $item->getQtyBeforeChange();

            // Process Add to Cart or Remove from Cart based on quantity change
            if ($qtyChange != 0) {
                if ($qtyChange > 0) {
                    // Handle Add to Cart
                    $addToCartPushData = $this->datablock->getAddtocartJsonData(
                        $qtyChange,
                        $item->getProduct(),
                        $item->getBuyRequest()->getData(),
                        true
                    );
                    $this->updateSessionData('GA4AddToCartData', $addToCartPushData);
                } else {
                    // Handle Remove from Cart
                    $removeFromCartPushData = $this->datablock->getRemovefromcartJsonData(
                        abs($qtyChange),
                        $item->getProduct(),
                        $item
                    );
                    $this->updateSessionData('GA4RemoveFromCartData', $removeFromCartPushData);
                }
            }
        }

        return $this;
    }

    /**
     * Update session data with combined JSON data.
     *
     * @param string $key The key of the session data to be updated.
     * @param array $data The data to be combined and updated in the session.
     */
    private function updateSessionData($key, $data)
    {
        $currentData = $this->checkoutSession->getData($key);
        $newData = $this->datablock->combineAddtocartJsonData($currentData, $data);
        $this->checkoutSession->setData($key, $newData);
    }
}
