<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Plugin;

class AddtoCartFromWishlist
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
     * AddToCart Plugin
     *
     * @param \Magento\Wishlist\Model\Item $subject
     * @param array $result
     * @return bool
     * @throws \Magento\Catalog\Model\Product\Exception
     */
    public function afterAddToCart(
        \Magento\Wishlist\Model\Item $subject,
        $result
    ) {
        if (!$this->helper->isModuleEnabled()) {
            return $result;
        }

        if ($result) {
            $buyRequest = $subject->getBuyRequest();
            $qty = $buyRequest->getData('qty');
            $product = $subject->getProduct();

            $addToCartJsonData = $this->datablock->getAddtocartJsonData($qty, $product, $buyRequest, true);
            $this->updateSessionData('GA4AddToCartData', $addToCartJsonData);

        }

        return $result;
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
