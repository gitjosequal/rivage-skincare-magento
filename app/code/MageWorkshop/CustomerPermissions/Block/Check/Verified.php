<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Block\Check;

use Magento\Review\Block\Product\View;

class Verified extends \MageWorkshop\DetailedReview\Block\Review\Product\View\Rating\AbstractRating
{
    /**
     * @var \MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper
     */
    private $verifiedHelper;

    /**
     * Verified constructor.
     * @param \MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->verifiedHelper = $verifiedHelper;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $isValid = false;
        $product = $this->_coreRegistry->registry('product');

        if ($product && $product->getId()) {
            $isValid = $this->verifiedHelper->allowToPostReviewForCurrentUser($product->getId());
        }

        return $isValid;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->isValid() ? '' : View::_toHtml();
    }

    /**
     * @return bool
     */
    public function getCustomerId()
    {
        return $this->verifiedHelper->getCustomerModel()->getId();
    }

    /**
     * @return string
     */
    public function getPermissionsCheckUrl()
    {
        return $this->getUrl('mageworkshop_customerpermissions/check/rules');
    }

    /**
     * There is nothing to show on the page if product has no reviews. The form must be visible.
     * @return bool
     */
    public function canCancelVerification()
    {
        return (bool) $this->getReviewsCollection()->getSize();
    }

    /**
     * @return bool
     */
    public function isVerificationRequest()
    {
        return (bool) $this->_request->getParam('verify-email', false);
    }
}
