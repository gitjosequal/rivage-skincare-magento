<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddProductToWishlistObserver implements ObserverInterface
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
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Constructor
     *
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Rivage\GtmExtension\Block\Datalayer $datablock
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper,
        \Rivage\GtmExtension\Block\Datalayer $datablock,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->helper = $helper;
        $this->datablock = $datablock;
        $this->customerSession = $customerSession;
        $this->productFactory = $productFactory;
    }

    /**
     * Executes the observer logic when a product is added to the wishlist.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        /** Current Product Object */
        $product = $observer->getEvent()->getProduct();

        /** Current Magento\Wishlist\Model\Item Object */
        $item = $observer->getEvent()->getItem();

        $buyRequestData = $item->getBuyRequest()->getData();

        $this->processWishlistItem($product, $buyRequestData, $item);

        return $this;
    }

    /**
     * Processes a wishlist item and prepares JSON data for GA4 tracking.
     *
     * @param \Magento\Catalog\Model\Product $product The product being added to the wishlist.
     * @param array $buyRequestData The buy request data for the product.
     * @param \Magento\Wishlist\Model\Item $item The wishlist item being processed.
     */
    private function processWishlistItem($product, $buyRequestData, $item)
    {
        $wishlistData = $this->datablock->prepareWishListJsonData($product, $buyRequestData, $item);

        $this->customerSession->setGA4WishListJsonData($wishlistData);
    }
}
