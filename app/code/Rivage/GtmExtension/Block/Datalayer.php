<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Block;

use Rivage\GtmExtension\Model\Enum;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class \Rivage\GtmExtension\Block\ViewProduct
 */
class Datalayer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var storeCategoryData
     */
    protected $storeCategoryData;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

     /**
      * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
      */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Rivage\GtmExtension\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\DataObject\Factory $objectFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->objectFactory = $objectFactory;
        $this->storeCategoryData = [];
        parent::__construct($context);
    }

    /**
     * Retrieve the data layer object in JSON format.
     *
     * @return string JSON-encoded data layer object.
     */
    public function getdatalayerobject()
    {
        $datalayerobject = [];
        $datalayerobject[] = $this->getDefaultInformation();

        return json_encode($datalayerobject);
    }

    /**
     * Retrieve the default information for the data layer object based on the current page action.
     *
     * @return array Data layer information in array format.
     */
    public function getDefaultInformation()
    {
        $datalayerData = [];
        $datalayerData['pageName'] =  $this->getLayout()->getBlock('page.main.title') ?$this->getLayout()->getBlock('page.main.title')->getPageTitle():'';
        $datalayerData['pageType'] =  $this->getPageType();
        $actionName = $this->helper->getControllerActionName();
        if ($actionName == 'catalog_category_view') {
            $category = $this->helper->getCurrentCategory();
            $datalayerData['pageName'] = $category?$category->getName():'';
            $datalayerData['ecommerce']['items'] = $this->prepareCategoryJsonData();
            $datalayerData['event'] = 'view_item_list';
            return $datalayerData;
        }
        if ($actionName == 'catalog_product_view') {
            $product = $this->helper->getCurrentProduct();
            $datalayerData['ecommerce']['items'] = $this->prepareProductJsonData();
            $datalayerData['event'] = 'view_item';
            $datalayerData['value'] = $product ? $this->getFormatedPrice($product->getFinalPrice()):0;
            return $datalayerData;
        }
        if ($actionName == 'checkout_cart_index') {
            $datalayerData['ecommerce']['items'] = $this->prepareCartJsonData();
            $datalayerData['event'] = 'view_cart';
            $datalayerData['cart_total'] = $this->getGrandTotalOfCart();
            $datalayerData['value'] = $this->getGrandTotalOfCart();
            $datalayerData['total'] = $this->getGrandTotalOfCart();
            $datalayerData['currency'] = $this->helper->getCurrencyCode();
            return $datalayerData;
        }
        if ($actionName == 'checkout_index_index') {
            $datalayerData['ecommerce']['items'] = $this->prepareCheckoutJsonData();
            $datalayerData['event'] = 'begin_checkout';
            $datalayerData['cart_total'] = $this->getGrandTotalOfCart();
            $datalayerData['value'] = $this->getGrandTotalOfCart();
            $datalayerData['currency'] = $this->helper->getCurrencyCode();
            return $datalayerData;
        }
        if ($actionName == 'checkout_onepage_success') {
            $datalayerData['ecommerce']['purchase'] = $this->prepareOrderJsonData();
            $datalayerData['event'] = 'purchase';
            $datalayerData['value'] = $this->getGrandTotalOfCart();
            return $datalayerData;
        }

        return $datalayerData;
    }

    /**
     * Retrieve the page type based on the current controller action.
     *
     * @return string Page type identifier.
     */
    public function getPageType()
    {
        $actionName = $this->helper->getControllerActionName();
        if (isset(Enum::PAGETYPE_DATA[$actionName])) {
            return Enum::PAGETYPE_DATA[$actionName];
        } else {
            return 'other';
        }
    }

    /**
     * Prepare an array of product data for the current category as JSON.
     *
     * @return array JSON
     */
    public function prepareCategoryJsonData()
    {
        $ecommerce = [];
        $category = $this->helper->getCurrentCategory();
        $productCollection = $this->getProductCollection();
        if ($category) {
            if (count($productCollection)) {
                $i = 1;
                foreach ($productCollection as $product) {
                    $categoryProduct = [];
                    $categoryProduct['item_name'] = $product->getName();
                    $categoryProduct['item_id'] = $product->getSku();
                    $categoryProduct['price'] = $this->getFormatedPrice($product->getFinalPrice());
                    // @codingStandardsIgnoreStart
                    $categoryProduct = array_merge($categoryProduct, $this->getProductCategories($product));
                    // @codingStandardsIgnoreEnd
                    $categoryProduct['item_list_name'] = $category->getName();
                    $categoryProduct['item_list_id'] = $category->getId();
                    $categoryProduct['index'] = $i;
                    $categoryProduct['currency'] = $this->helper->getCurrencyCode();
                    $ecommerce[] = $categoryProduct;
                    $i++ ;
                }
            }
        }
        return $ecommerce;
    }

    /**
     * Prepare an array of product data for the current product as JSON.
     *
     * @return array JSON
     */
    public function prepareProductJsonData()
    {
        $ecommerce = [];
        $product = $this->helper->getCurrentProduct();
        if (isset($product)) {
            $productDetails = [];
            $productDetail = [];
            $productDetail['item_name'] = $product->getName();
            $productDetail['item_id'] = $product->getSku();
            $productDetail['price'] = $this->getFormatedPrice($product->getFinalPrice());
            // @codingStandardsIgnoreStart
            $productDetail = array_merge($productDetail, $this->getProductCategories($product));
            // @codingStandardsIgnoreEnd
            $productDetail['currency'] = $this->helper->getCurrencyCode();
            $ecommerce[] = $productDetail;
        }
        return $ecommerce;
    }

    /**
     * Prepare an array of cart data for the current cart items as JSON.
     *
     * @return array JSON
     */
    public function prepareCartJsonData()
    {
        $quote = $this->getQuote();
        $ecommerce = [];
        foreach ($quote->getAllVisibleItems() as $item) {

            $product = $item->getProduct();
    
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $item->getChildren();
                foreach ($children as $child) {
                    $productIdModel = $child->getProduct();
                }
            }

            $productDetail = [];
            $productDetail['currency'] = $this->helper->getCurrencyCode();
            $productDetail['item_name'] = $item->getName();
            $productDetail['item_id'] = $item->getId();
            $productDetail['price'] = $this->getFormatedPrice($item->getPriceInclTax());
            $productCategoryIds = $product->getCategoryIds();
            $categoryName =  $this->getCategoryFromId($productCategoryIds);
            // @codingStandardsIgnoreStart
            $productDetail = array_merge($productDetail, $this->getProductCategories($product));
            // @codingStandardsIgnoreEnd
            $productDetail['item_list_name'] = $categoryName;
            $productDetail['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
            $productDetail['quantity'] = $item->getQty();
            $ecommerce[] = $productDetail;
        }

        return $ecommerce;
    }

    /**
     * Combines the current add-to-cart JSON data with new addToCart push data.
     *
     * @param array $currentAddToCartData The current add-to-cart JSON data.
     * @param array $addToCartPushData The new addToCart push data to combine.
     * @return array
     */
    public function combineAddtocartJsonData($currentAddToCartData, $addToCartPushData)
    {
        if (!is_array($currentAddToCartData)) {
            $currentAddToCartData = $addToCartPushData;
        } else {
            // @codingStandardsIgnoreStart
            $currentAddToCartData['ecommerce']['action']['items'][] = $addToCartPushData['ecommerce']['action']['items'][0];
            // @codingStandardsIgnoreEnd
        }

        return $currentAddToCartData;
    }

    /**
     * Generates the add-to-cart JSON data for a product.
     *
     * @param int|float $qty The quantity of the product being added to the cart.
     * @param \Magento\Catalog\Model\Product $product The product being added to the cart.
     * @param array $buyRequest Additional buy request data (optional).
     * @param bool $checkForCustomOptions Flag to check for custom options (optional).
     * @return array
     */
    public function getAddtocartJsonData($qty, $product, $buyRequest = [], $checkForCustomOptions = false)
    {
        $result = [];

        /*if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $canditatesRequest = $this->objectFactory->create($buyRequest);
            $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($canditatesRequest, $product);

            foreach ($cartCandidates as $candidate) {
                if ($candidate->getParentProductId()) {
                    $productId = $this->helper->getGtmProductId($candidate);
                }
            }
        }*/

        $result['event'] = 'add_to_cart';
        $result['ecommerce'] = [];
        $result['ecommerce']['action'] = [];
        $result['ecommerce']['action']['items'] = [];

        $productData = [];
        $productData['item_name'] = $product->getName();
        $productData['item_id'] = $product->getSku();
        $productData['price'] = $this->getFormatedPrice($product->getFinalPrice());
        $productCategoryIds = $product->getCategoryIds();
        $categoryName =  $this->getCategoryFromId($productCategoryIds);
        // @codingStandardsIgnoreStart
        $productData = array_merge($productData, $this->getProductCategories($product));
        // @codingStandardsIgnoreEnd
        $productData['item_list_name'] = $categoryName;
        $productData['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
        $productData['quantity'] = (double)$qty;
        $productData['currency'] = $this->helper->getCurrencyCode();

        $result['ecommerce']['action']['items'][] = $productData;

        return $result;
    }

    /**
     * Prepares add-to-wishlist JSON data for a product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $buyRequestData Additional buy request data.
     * @param string $item The item (optional).
     * @return array
     */
    public function prepareWishListJsonData($product, $buyRequestData, $item = '')
    {
        $result = [];

        $result['event'] = 'add_to_wishlist';
        $result['ecommerce'] = [];
        $result['ecommerce']['action'] = [];
        $result['ecommerce']['action']['items'] = [];

        $productDetail = [];
        $productDetail['currency'] = $this->helper->getCurrencyCode();
        $productDetail['item_name'] = $product->getName();
        $productDetail['item_id'] = $product->getSku();
        $productDetail['price'] = $this->getFormatedPrice($product->getFinalPrice());
        $productCategoryIds = $product->getCategoryIds();
        $categoryName =  $this->getCategoryFromId($productCategoryIds);
        // @codingStandardsIgnoreStart
        $productDetail = array_merge($productDetail, $this->getProductCategories($product));
        // @codingStandardsIgnoreEnd
        $productDetail['item_list_name'] = $categoryName;
        $productDetail['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';

        $result['ecommerce']['action']['items'][] = $productDetail;

        return $result;
    }

    /**
     * Prepares remove-from-cart JSON data for a product.
     *
     * @param float $qty The quantity of the product being removed.
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return array
     */
    public function getRemovefromcartJsonData($qty, $product, $quoteItem)
    {
        $result = [];

        $productId = $this->getGtmProductId($product);

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $canditatesRequest = $this->objectFactory->create($buyRequest);
            $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($canditatesRequest, $product);

            foreach ($cartCandidates as $candidate) {
                if ($candidate->getParentProductId()) {
                    $productId = $this->helper->getGtmProductId($candidate);
                }
            }
        }

        $result['event'] = 'remove_from_cart';
        $result['ecommerce'] = [];
        $result['ecommerce']['action'] = [];
        $result['ecommerce']['action']['items'] = [];

        $productData = [];
        $productData['item_name'] = $product->getName();
        $productData['item_id'] = $productId;
        $productData['price'] = $this->getFormatedPrice($product->getFinalPrice());
        $productCategoryIds = $product->getCategoryIds();
        $categoryName =  $this->getCategoryFromId($productCategoryIds);
        // @codingStandardsIgnoreStart
        $productData = array_merge($productData, $this->getProductCategories($product));
        // @codingStandardsIgnoreEnd
        $productData['item_list_name'] = $categoryName;
        $productData['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
        $productData['quantity'] = (double)$qty;
        $productData['currency'] = $this->helper->getCurrencyCode();

        $result['ecommerce']['action']['items'][] = $productData;

        return $result;
    }

    /**
     * Prepares checkout JSON data for the products in the cart.
     *
     * @return array The prepared checkout JSON data.
     */
    public function prepareCheckoutJsonData()
    {
        $quote = $this->getQuote();
        $ecommerce = [];
        foreach ($quote->getAllVisibleItems() as $item) {

            $product = $item->getProduct();
    
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $item->getChildren();
                foreach ($children as $child) {
                    $productIdModel = $child->getProduct();
                }
            }

            $productDetail = [];
            $productDetail['currency'] = $this->helper->getCurrencyCode();
            $productDetail['item_name'] = $item->getName();
            $productDetail['item_id'] = $item->getId();
            $productDetail['price'] = $this->getFormatedPrice($item->getPriceInclTax());
            $productCategoryIds = $product->getCategoryIds();
            $categoryName =  $this->getCategoryFromId($productCategoryIds);
            // @codingStandardsIgnoreStart
            $productDetail = array_merge($productDetail, $this->getProductCategories($product));
            // @codingStandardsIgnoreEnd
            $productDetail['item_list_name'] = $categoryName;
            $productDetail['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
            $productDetail['quantity'] = $item->getQty();
            $ecommerce[] = $productDetail;
        }

        return $ecommerce;
    }

    /**
     * Retrieves and prepares an array of order products for GA4 event tracking.
     *
     * @return array
     */
    public function getOrderProducts()
    {
        $order = $this->getOrder();
        $ecommerce = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productIdModel = $product;
            
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $item->getChildrenItems();
                foreach ($children as $child) {
                    $productIdModel = $child->getProduct();
                }
            }

            $productDetail = [];
            $productDetail['currency'] = $this->helper->getCurrencyCode();
            $productDetail['item_name'] = $item->getName();
            $productDetail['item_id'] = $this->helper->getGtmProductId($productIdModel);
            $productDetail['price'] = $this->getFormatedPrice($item->getPriceInclTax());
            $productCategoryIds = $product->getCategoryIds();
            $categoryName =  $this->getCategoryFromId($productCategoryIds);
            // @codingStandardsIgnoreStart
            $productDetail = array_merge($productDetail, $this->getProductCategories($product));
            // @codingStandardsIgnoreEnd
            $productDetail['item_list_name'] = $categoryName;
            $productDetail['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
            $productDetail['quantity'] = (double)$item->getQtyOrdered();
            $ecommerce[] = $productDetail;

        }

        return $ecommerce;
    }

    private function getFormatedPrice($value){
      return number_format($value, 2, '.', '');  
    }
    
    /**
     * Prepares JSON data for an order's purchase event.
     *
     * @return array
     */
    public function prepareOrderJsonData()
    {
        $order = $this->getOrder();
        $orderproducts = $this->getOrderProducts();
        $purchaseData = [
            'transaction_id' => $order->getIncrementId(),
            'value' => $this->getFormatedPrice($order->getGrandTotal()),
            'coupon' => $order->getCouponCode(),
            'tax' => $this->getFormatedPrice($order->getTaxAmount()),
            'shipping' => $this->getFormatedPrice($order->getShippingAmount()),
            'currency' => $this->helper->getCurrencyCode(),
            'total_order_count' => $this->getTotalOrderOfCustomer(),
            'total_lifetime_value' => $this->getFormatedPrice($this->calculateTotalValue())
        ];
        $purchaseData['items'] = $orderproducts;

        return $purchaseData;
    }

    /**
     * Calculates and returns the total lifetime value for a customer.
     *
     * @return float
     */
    public function calculateTotalValue()
    {
        $order = $this->getOrder();
        $customerId = $order->getCustomerId();

        if (!$customerId) {
            return $order->getGrandtotal();
        }

        $orderTotals = $this->orderCollectionFactory->create($customerId)
            ->addFieldToSelect(['grand_total', 'total_refunded']);

        $grandTotalSum = 0;
        $refundTotalSum = 0;

        foreach ($orderTotals as $orderItem) {
            $grandTotalSum += $orderItem->getGrandTotal();
            $refundTotalSum += $orderItem->getTotalRefunded();
        }

        return $grandTotalSum - $refundTotalSum;
    }

    /**
     * Calculates and returns the total number of orders for a customer.
     *
     * @return int The total number of orders for the customer.
     */
    public function getTotalOrderOfCustomer()
    {
        $order = $this->getOrder();
        $customerId = $order->getCustomerId();
        
        if (!$customerId) {
            return 1; // If no customer ID is available, assume there's at least one order.
        }

        $orderCount = $this->orderCollectionFactory->create($customerId)
            ->addFieldToSelect('entity_id')
            ->count();

        return $orderCount;
    }

    /**
     * Retrieves and returns the order object associated with the last placed order in the session.
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrder()
    {
        $lastOrderId = $this->checkoutSession->getLastOrderId();
        if (!$lastOrderId) {
            return;
        }

        $order = $this->orderRepository->get($lastOrderId);

        return $order;
    }

    /**
     * Retrieves a collection of products associated with the current category.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getProductCollection()
    {
        $category = $this->helper->getCurrentCategory();
        if (!$category) {
            return null;
        }

        $collection = $category->getProductCollection();
        $collection->addAttributeToSelect('*');
        $collection->setCurPage($this->getCurrentPage())->setPageSize($this->getPageSize());

        return $collection;
    }

    /**
     * Retrieves the page size for the product collection.
     *
     * @return int
     */
    public function getPageSize()
    {
        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $productListBlockToolbar */
        $productListBlockToolbar = $this->_layout->getBlock('product_list_toolbar');
        if (empty($productListBlockToolbar)) {
            return 12;
        }

        return (int) $productListBlockToolbar->getLimit();
    }

    /**
     * Retrieves the current page number from the request parameter.
     *
     * @return int
     */
    protected function getCurrentPage()
    {
        $page = (int) $this->_request->getParam('p');
        if (!$page) {
            return 1;
        }

        return $page;
    }

    /**
     * Retrieves an associative array of product category names indexed by item_category keys.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductCategories($product)
    {
        $catid = $product->getCategoryIds();
        $catname = [];
        $index = 1;

        foreach ($catid as $key => $cat) {
            $indexvalue = $key ? $index : '';
            $catname['item_category'.$indexvalue] = $this->helper->getCatNameFromId($cat)->getName();
            $index++;
        }

        return $catname;
    }

    /**
     * Retrieves the current quote object from the checkout session.
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Fetches and stores category information specific to the current store.
     */
    private function fetchStoreCategoryInfo()
    {
        if (!empty($this->storeCategoryData)) {
            return;
        }

        $rootCategoryId = $this->getRootCategoryId();
        $storeId = $this->getCurrentStore();

        $categorycollection = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('path', ['like' => "1/{$rootCategoryId}%"])
            ->addAttributeToSelect('name');

        foreach ($categorycollection as $cat) {
            $this->storeCategoryData[$cat->getEntityId()] = [
                'name' => $cat->getName(),
                'path' => $cat->getPath()
            ];
        }
    }

     /**
      * Retrieves a Google Tag Manager (GTM) compatible category path based on provided category IDs.
      *
      * @param array $categoryIds
      * @return string
      */
    public function getCategoryFromId($categoryIds)
    {
        if (!count($categoryIds)) {
            return '';
        }

        if (empty($this->storeCategoryData)) {
            $this->fetchStoreCategoryInfo();
        }

        $categoryPath = '';
        foreach ($categoryIds as $categoryId) {
            if (isset($this->storeCategoryData[$categoryId])) {
                $categoryPath = $this->storeCategoryData[$categoryId]['path'];
                break;
            }
        }

        return $this->generateCategoryPathFromIds($categoryPath);
    }

    /**
     * Generates a category path string from category IDs by excluding ignored categories.
     *
     * @param string $categoryPath
     * @return string
     */
    private function generateCategoryPathFromIds($categoryPath)
    {
        $categoryIds = explode('/', $categoryPath);
        $categoryNames = $this->getCategoryNames($categoryIds);

        return implode('/', $categoryNames);
    }

    /**
     * Retrieves the root category ID of the current store.
     *
     * @return int
     */
    protected function getRootCategoryId()
    {
        return $this->storeManager->getStore()->getRootCategoryId();
    }

    /**
     * Retrieves the ID of the current store.
     *
     * @return int
     */
    protected function getCurrentStore()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieves the names of categories based on provided category IDs and the number of ignored categories.
     *
     * @param array $categoryIds
     * @return array
     */
    private function getCategoryNames($categoryIds)
    {
        $categoryNames = [];

        foreach ($categoryIds as $categoryId) {
            $category = $this->storeCategoryData[$categoryId] ?? null;
            
            if ($category) {
                $categoryNames[] = $category['name'];
            }
        }

        return $categoryNames;
    }

   /**
    * Retrieves the total value of the cart during the checkout process.
    *
    * @return float
    */
    public function getGrandTotalOfCart()
    {
        $quote = $this->getQuote();
        return $this->getFormatedPrice($quote->getGrandTotal());
    }
}
