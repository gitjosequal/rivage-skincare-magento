<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Plugin\CustomerData;

use Magento\Framework\DataObject;
use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Customer Plugin
 */
class Ga4Data extends DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($data);
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieves the section data containing JSON-encoded tracking data for different events.
     *
     * @return array
     */
    public function getSectionData()
    {
        $data = [];

        //For Add to Cart
        $addToCartData = $this->checkoutSession->getData('GA4AddToCartData');
        if ($addToCartData) {
            $data[] = $addToCartData;
            $this->checkoutSession->unsetData('GA4AddToCartData');
        }

        // For Remove From Cart
        $removeFromCartData = $this->checkoutSession->getData('GA4RemoveFromCartData');
        if ($removeFromCartData) {
            $data[] = $removeFromCartData;
            $this->checkoutSession->unsetData('GA4RemoveFromCartData');
        }

       // For Wishlist From Cart
        $wishlistData = $this->customerSession->getGA4WishListJsonData();

        if ($wishlistData) {
            $data[] = $wishlistData;
            $this->customerSession->setGA4WishListJsonData(null);
        }
        
        return [
            'datalayer' => $this->jsonHelper->jsonEncode($data)
        ];
    }
}
