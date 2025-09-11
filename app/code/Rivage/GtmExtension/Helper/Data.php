<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    // @codingStandardsIgnoreStart
    const XML_PATH_ENABLED = 'rivage_gtmextension/general/enable';
    const XML_JS_CODE = 'rivage_gtmextension/general/gtm_js_code';
    const XML_NONJS_CODE = 'rivage_gtmextension/general/gtm_nonjs_code';
    const XML_PROMOTION_TRACKING = 'rivage_gtmextension/promotion_tracking_config/promotion_tracking';
    // @codingStandardsIgnoreEnd

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var array
     */
    protected $storeCategoryData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categorymodel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Catalog\Model\CategoryFactory $categorymodel
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Model\CategoryFactory $categorymodel,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($context);
        $this->blockFactory = $blockFactory;
        $this->pageConfig = $pageConfig;
        $this->registry = $registry;
        $this->storeCategoryData = [];
        $this->storeManager = $storeManager;
        $this->layout = $layout;
        $this->checkoutSession = $checkoutSession;
        $this->currency = $currency;
        $this->categorymodel = $categorymodel;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Check if the custom module is enabled.
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
         return $this->scopeConfig->getValue(
             self::XML_PATH_ENABLED,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * Get the Google Tag Manager code snippet.
     *
     * @return string
     */
    public function getGtmJsCode()
    {
         return $this->scopeConfig->getValue(
             self::XML_JS_CODE,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * Get the Google Tag Manager non-JavaScript code snippet.
     *
     * @return string
     */
    public function getGtmNonJsCode()
    {
         return $this->scopeConfig->getValue(
             self::XML_NONJS_CODE,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * Check if promotion tracking is enabled.
     *
     * @return bool
     */
    public function isPromotionTrackingEnabled()
    {
         return $this->scopeConfig->getValue(
             'rivage_gtmextension/promotion_tracking_config/promotion_tracking',
             ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * Retrieve the current category from registry.
     *
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Retrieve the root category ID of the current store.
     *
     * @return int
     */
    protected function getRootCategoryId()
    {
        return $this->storeManager->getStore()->getRootCategoryId();
    }

    /**
     * Get the current store ID.
     *
     * @return int
     */
    protected function getCurrentStore()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get the Product Click Data Object.
     *
     * @param array $product
     * @return Json
     */
    public function getclickdataobject($product)
    {
        $datalayerobject = $this->getProductClickEvent($product);
        return json_encode($datalayerobject);
    }

    /**
     * Get the Product Click Event.
     *
     * @param array $product
     * @return array
     */
    public function getProductClickEvent($product)
    {
        $datalayerData = [];
        $datalayerData['pageName'] =  'Product Click';
        $datalayerData['pageType'] =  'ProductClick';
        $datalayerData['ecommerce']['items'] = $this->prepareProductClickEvent($product);
        $datalayerData['event'] = 'select_item';
        return $datalayerData;
    }

    /**
     * Get the Prepare Data For Product Click Event.
     *
     * @param array $product
     * @return array
     */
    public function prepareProductClickEvent($product)
    {
        $ecommerce = [];
        if (isset($product)) {
            $productDetails = [];
            $productDetail = [];
            $productDetail['item_name'] = $product->getName();
            $productDetail['item_id'] = $product->getSku();
            $productDetail['price'] =
            $this->getFormattedCurrency($product->getPriceInfo()->getPrice('final_price')->getValue());
            $productDetail['currency'] = $this->getCurrencyCode();
            $ecommerce[] = $productDetail;
        }
        return $ecommerce;
    }

    /**
     * Get the Google Tag Manager product ID based on the product SKU.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtmProductId($product)
    {
        $gtmProductId = '';
        $gtmProductId = $product->getData('sku');
        
        return $gtmProductId;
    }

    /**
     * Get the current currency code for the store.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get the currently viewed product from the registry.
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get the full name of the currently executed controller action.
     *
     * @return string
     */
    public function getControllerActionName()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * Format the given price into a formatted currency string.
     *
     * @param float $price
     * @return string
     */
    public function getFormattedCurrency($price)
    {
        return $this->currency->format($price, ['display'=>1], false);
    }

    /**
     * Retrieve the category name from the given category ID.
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCatNameFromId($categoryId)
    {
        return $this->categorymodel->create()->load($categoryId);
    }
}
