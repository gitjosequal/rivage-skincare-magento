<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Josequal\Override\Magento\GoogleAnalytics\Block;

/**
 * GoogleAnalytics Page Block
 *
 * @api
 * @since 100.0.2
 */
class Ga extends \Magento\GoogleAnalytics\Block\Ga
{
    /**
     * Return information about order and items for GA tracking.
     *
     * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#checkout-options
     * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#measuring-transactions
     * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#transaction
     *
     * @return array
     * @since 100.2.0
     */
    public function getOrdersTrackingData()
    {
        $result = [];
        $orderIds = $this->getOrderIds();

        if (empty($orderIds) || !is_array($orderIds)) {
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $urlInterface = $objectManager->create('\Magento\Framework\UrlInterface');
            
            if(strpos($urlInterface->getCurrentUrl(), 'success') !== false){
                $orderData = $objectManager->create('\Magento\Checkout\Model\Session');
                $orderFactory =  $objectManager->create('\Magento\Sales\Model\OrderFactory');
                $order = $orderFactory->create()->loadByIncrementId($orderData->getLastRealOrderId());
                foreach ($order->getAllVisibleItems() as $item) {
                    $result['products'][] = [
                        'id' => $this->escapeJsQuote($item->getSku()),
                        'name' =>  $this->escapeJsQuote($item->getName()),
                        'price' => $item->getPrice(),
                        'quantity' => $item->getQtyOrdered(),
                    ];
                }
                $result['orders'][] = [
                    'id' =>  $order->getIncrementId(),
                    'affiliation' => $this->escapeJsQuote($this->_storeManager->getStore()->getFrontendName()),
                    'revenue' => $order->getGrandTotal(),
                    'tax' => $order->getTaxAmount(),
                    'shipping' => $order->getShippingAmount(),
                ];
                $result['currency'] = $order->getOrderCurrencyCode();
            }
            return $result;
         }

        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        
       

        foreach ($collection as $order) {
             var_dump($order->getAllVisibleItems());die;
            foreach ($order->getAllVisibleItems() as $item) {
                $result['products'][] = [
                    'id' => $this->escapeJsQuote($item->getSku()),
                    'name' =>  $this->escapeJsQuote($item->getName()),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQtyOrdered(),
                ];
            }
            $result['orders'][] = [
                'id' =>  $order->getIncrementId(),
                'affiliation' => $this->escapeJsQuote($this->_storeManager->getStore()->getFrontendName()),
                'revenue' => $order->getGrandTotal(),
                'tax' => $order->getTaxAmount(),
                'shipping' => $order->getShippingAmount(),
            ];
            $result['currency'] = $order->getOrderCurrencyCode();
        }
        return $result;
    }
}
