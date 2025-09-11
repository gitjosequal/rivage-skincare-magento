<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer\Review;

class PrepareSearchCollection extends AbstractPrepareCollection
{
    const SEARCH_REQUEST_PARAM = 'review_search';

    const SEARCH_MIN_QUANTITY_SYMBOL = 3;

    /**
     * @var array \MageWorkshop\DetailedReview\Helper\AbstractAttribute $notEmptyAttributesInReview
     */
    protected $notEmptyAttributesInReview = [];

    /** @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper */
    private $attributeHelper;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
    ) {
        $this->attributeHelper = $attributeHelper;
        parent::__construct($request);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!($search = $this->getRequestParam(self::SEARCH_REQUEST_PARAM))
            || (strlen($search) < self::SEARCH_MIN_QUANTITY_SYMBOL)
        ) {
            return;
        }

        /** @var \Magento\Review\Model\ResourceModel\Review\Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $attributes = $this->attributeHelper->getReviewFormAttributes($observer->getEvent()->getData('product'));
        $attributes->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea']]);
        $attributeCodes = $attributes->getColumnValues('attribute_code');

        $field = array_combine($attributeCodes, $attributeCodes);
        $condition = array_fill_keys($attributeCodes, ['like' => "%$search%"]);
        $collection->addFieldToFilter($field, $condition);
    }
}
