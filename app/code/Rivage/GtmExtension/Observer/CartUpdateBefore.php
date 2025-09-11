<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class CartUpdateBefore implements ObserverInterface
{
    /**
     * @var \Rivage\GtmExtension\Helper\Data
     */
    protected $helper;

    /**
     * @param \Rivage\GtmExtension\Helper\Data $helper
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    /**
     * Observer for cart item update event, capturing quantities before changes.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        // Get the cart data and the cart item collection
        $cart = $observer->getInfo()->getData();
        $cartdata = $observer->getCart();

        // Loop through each item in the cart
        foreach ($cart as $id => $data) {
            $cartitem = $cartdata->getQuote()->getItemById($id);

            if (!$cartitem) {
                continue;
            }
           
            // Skip if the item quantity is set to zero
            if (isset($data['qty']) && $data['qty'] == '0') {
                continue;
            }

            // Set the quantity before the change
            $cartitem->setQtyBeforeChange($cartitem->getQty());
        }

        return $this;
    }
}
