<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View\Filters;

use Magento\Catalog\Model\Product;
use MageWorkshop\DetailedReview\Observer\Review\PrepareSearchCollection;

class Form extends \Magento\Framework\View\Element\Template
{
    const ROUTE_TO_SEND_AJAX = 'mageworkshop_detailedreview/review/prepareReviewCollection';

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $catalogHelper;

    /**
     * ReviewFiltersWrapper constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data
    ) {
        $this->reviewHelper = $reviewHelper;
        $this->catalogHelper = $catalogHelper;
        parent::__construct($context, $data);
    }

    /**
     * Don't render this block if no reviews id present
     * @return string
     */
    public function _toHtml()
    {
        /** @var Product $product */
        $html = '';
        $product = $this->catalogHelper->getProduct();

        if ($product && $this->reviewHelper->getProductApprovedReviewsCount($product)) {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getReviewsApiUrl()
    {
        return $this->getUrl(
            self::ROUTE_TO_SEND_AJAX,
            ['id' => $this->catalogHelper->getProduct()->getId()]
        );
    }

    /**
     * @return string
     */
    public function getSearchParamName()
    {
        return PrepareSearchCollection::SEARCH_REQUEST_PARAM;
    }
}
