<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

class DateFilterInterval
{
    const FILTER_LAST_WEEK = '-1 week';
    const FILTER_LAST_MONTH = '-1 month';
    const FILTER_LAST_SIX_MONTHS_AGO = '-6 month';

    const FILTER_VALUE_FOR_ALL_REVIEWS   = 0;
    const FILTER_VALUE_FOR_LAST_WEEK     = 1;
    const FILTER_VALUE_FOR_LAST_MONTH    = 2;
    const FILTER_VALUE_FOR_LAST_6_MONTHS = 3;
    
    /** @var \Magento\Framework\Stdlib\DateTime\DateTime $date */
    protected $date;

    /**
     * DateForFilter constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @param $filter
     * @return string
     */
    public function getDateInterval($filter)
    {
        $date = '';

        switch ($filter) {
            case self::FILTER_VALUE_FOR_LAST_WEEK:
                $date = self::FILTER_LAST_WEEK;
                break;
            case self::FILTER_VALUE_FOR_LAST_MONTH:
                $date = self::FILTER_LAST_MONTH;
                break;
            case self::FILTER_VALUE_FOR_LAST_6_MONTHS:
                $date = self::FILTER_LAST_SIX_MONTHS_AGO;
                break;
            default:
                return $date;
        }

        return $this->date->date('Y-m-d H:i:s', strtotime($date));
    }
}
