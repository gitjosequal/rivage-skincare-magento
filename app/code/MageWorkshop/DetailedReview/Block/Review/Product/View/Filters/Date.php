<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View\Filters;

use MageWorkshop\DetailedReview\Helper\DateFilterInterval;
use MageWorkshop\DetailedReview\Observer\Review\PrepareFilterCollection;

class Date extends \Magento\Framework\View\Element\Template
{
    const FILTER_TITLE_LAST_WEEK     = 'Last Week';
    const FILTER_TITLE_LAST_MONTH    = 'Last Month';
    const FILTER_TITLE_LAST_6_MONTHS = 'Last 6 Months';
    const FILTER_TITLE_ALL_REVIEWS   = 'All Reviews';

    /**
     * @return array
     */
    public function getDateFilterOptions()
    {
        return [
            DateFilterInterval::FILTER_VALUE_FOR_LAST_WEEK     => self::FILTER_TITLE_LAST_WEEK,
            DateFilterInterval::FILTER_VALUE_FOR_LAST_MONTH    => self::FILTER_TITLE_LAST_MONTH,
            DateFilterInterval::FILTER_VALUE_FOR_LAST_6_MONTHS => self::FILTER_TITLE_LAST_6_MONTHS,
            DateFilterInterval::FILTER_VALUE_FOR_ALL_REVIEWS   => self::FILTER_TITLE_ALL_REVIEWS,
        ];
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isSelected($value)
    {
        $defaultValue = $this->getRequest()->getParam(
            $this->getFilterByDateParamName(),
            DateFilterInterval::FILTER_VALUE_FOR_ALL_REVIEWS
        );

        return $value === $defaultValue;
    }

    /**
     * @return string
     */
    public function getFilterByDateParamName()
    {
        return PrepareFilterCollection::FILTER_REQUEST_PARAM;
    }
}
