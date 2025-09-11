<?php
namespace Josequal\Override\Seo\Plugin;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Mageplaza\Seo\Model\Config\Source\PriceValidUntil;
use Magento\Review\Model\Review;
use Magento\Framework\DataObject;

class SeoRender extends \Mageplaza\Seo\Plugin\SeoRender
{
    public function showProductStructuredData()
    {
        if ($currentProduct = $this->getProduct()) {
            try {
                $priceAttributes = $this->collectionFactory->create()
                    ->addVisibleFilter()->addFieldToFilter('attribute_code', ['like' => "%price%"])
                    ->getColumnValues('attribute_code');
                $productId       = $currentProduct->getId() ?: $this->request->getParam('id');

                $product         = $this->productFactory->create()->load($productId);
                $availability    = $product->isAvailable() ? 'InStock' : 'OutOfStock';
                $stockItem       = $this->stockState->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );

                if ($sourceItemList = $this->sourceItemsBySku->execute($product->getSku())) {
                    $stockQty        = 0;
                    $websiteCode     = $this->_storeManager->getWebsite()->getCode();
                    $assignedStockId = $this->assignedStock->execute($websiteCode);

                    if ($product->getTypeId() === Configurable::TYPE_CODE) {
                        $typeInstance           = $product->getTypeInstance();
                        $childProductCollection = $typeInstance->getUsedProducts($product);
                        foreach ($childProductCollection as $childProduct) {
                            $qty = $this->salableQuantity->execute($childProduct->getSku());
                            foreach ($qty as $value) {
                                if ($value['stock_id'] == $assignedStockId) {
                                    $stockQty += isset($value['qty']) ? $value['qty'] : 0;
                                }
                            }
                        }
                    } else {
                        $qty = $this->salableQuantity->execute($product->getSku());
                        foreach ($qty as $value) {
                            if ($value['stock_id'] == $assignedStockId) {
                                $stockQty += isset($value['qty']) ? $value['qty'] : 0;
                            }
                        }

                    }

                    $stockItem = (int)$stockQty;
                }

                $priceValidUntil = $currentProduct->getSpecialToDate();
                $modelAttribute  = $this->helperData->getRichsnippetsConfig('model_value');
                $modelValue      = $product->getResource()
                    ->getAttribute($modelAttribute)
                    ->getFrontend()->getValue($product);
                if ($modelAttribute === 'quantity_and_stock_status') {
                    $modelValue = $this->helperData->getQtySale($product);
                }
                if ($modelValue && in_array($modelAttribute, $priceAttributes, true)) {
                    $modelValue = number_format($this->_priceHelper->currency($modelValue, false), 2);

                    if ($modelAttribute === 'price') {
                        $modelValue = $currentProduct->getPriceInfo()->getPrice('final_price')->getValue();
                    }
                }
                $modelName = $this->helperData->getRichsnippetsConfig('model_name');

                $productStructuredData = [
                    '@context'    => 'http://schema.org/',
                    '@type'       => 'Product',
                    'name'        => $currentProduct->getName(),
                    'description' => $currentProduct->getDescription() ? trim(strip_tags($currentProduct->getDescription())) : '',
                    'sku'         => $currentProduct->getSku(),
                    'url'         => $currentProduct->getProductUrl(),
                    'image'       => $this->getUrl('/media/catalog') . 'product' . $currentProduct->getImage(),
                    'offers'      => [
                        '@type'         => 'Offer',
                        'priceCurrency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'price'         => $currentProduct->getPriceInfo()->getPrice('final_price')->getValue(),
                        'itemOffered'   => is_integer($stockItem) ? $stockItem : $stockItem->getQty(),
                        'availability'  => 'http://schema.org/' . $availability,
                        'url'           => $currentProduct->getProductUrl()
                    ],
                    $modelName    => (($modelAttribute === 'quantity_and_stock_status' && $modelValue >= 0)
                        || $modelValue) ? $modelValue : $modelName
                ];
                $productStructuredData = $this->addProductStructuredDataByType(
                    $currentProduct->getTypeId(),
                    $currentProduct,
                    $productStructuredData
                );

                $priceValidType = $this->helperData->getRichsnippetsConfig('price_valid_until');
                if (!empty($priceValidUntil)) {
                    $productStructuredData['offers']['priceValidUntil'] = $priceValidUntil;
                } elseif ($priceValidType !== 'none') {
                    $time = $this->_dateTime->gmtTimestamp();

                    switch ($priceValidType) {
                        case PriceValidUntil::PLUS_7:
                            $time += 604800;
                            break;
                        case PriceValidUntil::PLUS_30:
                            $time += 2592000;
                            break;
                        case PriceValidUntil::PLUS_60:
                            $time += 5184000;
                            break;
                        case PriceValidUntil::PLUS_1_YEAR:
                            $time += 31536000;
                            break;
                        default:
                            $time = $this->helperData->getRichsnippetsConfig('price_valid_until_custom');
                            break;
                    }

                    $productStructuredData['offers']['priceValidUntil'] = $priceValidType === 'custom'
                        ? $time
                        : date('Y-m-d', $time);
                }

                if (!$this->_moduleManager->isEnabled('Mageplaza_Shopbybrand')
                    || !isset($productStructuredData['brand'])) {
                    $brandAttribute = $this->helperData->getRichsnippetsConfig('brand');
                    $brandValue     = $product->getResource()
                        ->getAttribute($brandAttribute)
                        ->getFrontend()->getValue($product);

                    if ($brandAttribute === 'quantity_and_stock_status') {
                        $brandValue = $this->helperData->getQtySale($product);
                    }

                    if ($brandValue && in_array($brandAttribute, $priceAttributes, true)) {
                        $brandValue = number_format($this->_priceHelper->currency($brandValue, false), 2);
                        if ($brandAttribute === 'price') {
                            $brandValue = $currentProduct->getPriceInfo()->getPrice('final_price')->getValue();
                        }
                    }

                    $productStructuredData['brand']['@type'] = 'Brand';
                    $productStructuredData['brand']['name']  = (($brandAttribute === 'quantity_and_stock_status'
                            && $brandValue >= 0) || $brandValue) ? $brandValue : 'Brand';
                }

                $collection = $this->_reviewCollection->create()
                    ->addStatusFilter(
                        Review::STATUS_APPROVED
                    )->addEntityFilter(
                        'product',
                        $product->getId()
                    )->addRateVotes()->setDateOrder();
                if ($collection->getSize()) {
                    foreach ($collection as $review) {
                        
                        $reviewData = [
                            '@type'  => 'Review',
                            'author' => [
                                '@type' => 'Person',
                                'name'  => $review->getNickname()
                            ]
                        ];
                        $votes = $review->getRatingVotes();
                        if (count($votes)) {
                            $voteSum = 0;
                            foreach($votes as $_vote){
                                $voteSum += $_vote->getPercent();
                            }
                            $vote                       = $voteSum / count($votes);
                            $reviewData['reviewRating'] = [
                                '@type'       => 'Rating',
                                'ratingValue' =>  $vote,
                                'bestRating'  => '100'
                            ];  
                        }
                        $productStructuredData['review'][] = $reviewData;
                    }
                }

                if ($this->getReviewCount()) {
                    $productStructuredData['aggregateRating']['@type']       = 'AggregateRating';
                    $productStructuredData['aggregateRating']['bestRating']  = 100;
                    $productStructuredData['aggregateRating']['worstRating'] = 0;
                    $productStructuredData['aggregateRating']['ratingValue'] = $this->getRatingSummary();
                    $productStructuredData['aggregateRating']['reviewCount'] = $this->getReviewCount();
                }

                $objectStructuredData = new DataObject(['mpdata' => $productStructuredData]);
                $this->_eventManager->dispatch(
                    'mp_seo_product_structured_data',
                    ['structured_data' => $objectStructuredData]
                );
                $productStructuredData = $objectStructuredData->getMpdata();

                return $this->helperData->createStructuredData(
                    $productStructuredData,
                    '<!-- Product Structured Data by Mageplaza SEO-->'
                );
            } catch (Exception $e) {
                $this->messageManager->addError(__('Can not add structured data'));
            }
        }

        return '';
    }
}