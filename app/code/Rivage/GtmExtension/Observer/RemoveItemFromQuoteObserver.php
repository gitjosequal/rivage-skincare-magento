<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class RemoveItemFromQuoteObserver implements ObserverInterface
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
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Rivage\GtmExtension\Block\Datalayer $datablock
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper,
        \Rivage\GtmExtension\Block\Datalayer $datablock,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->datablock = $datablock;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Observer For Removing Item From Quote
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
        $itemId = $item->getProductId();

        if (!$itemId) {
            return $this;
        }

        $product = $this->productRepository->getById($itemId);
        $qty = $item->getQty();

        /** Need to extend or use another event or plugin to send variant */
        $removeFromCartJsonData = $this->datablock->getRemovefromcartJsonData($qty, $product, $item);
        $this->checkoutSession->setData('GA4RemoveFromCartData', $removeFromCartJsonData);

        return $this;
    }
}
