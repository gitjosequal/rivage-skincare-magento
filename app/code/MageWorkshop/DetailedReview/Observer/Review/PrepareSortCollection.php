<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer\Review;

use Magento\Framework\DB\Select;

class PrepareSortCollection extends AbstractPrepareCollection
{
    const SORT_REQUEST_PARAM = 'review_sort_direction';
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\ResourceModel\Review\Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $direction = $this->getRequestParam(self::SORT_REQUEST_PARAM) === Select::SQL_ASC
            ? Select::SQL_ASC
            : Select::SQL_DESC;

        $collection->getSelect()->order('main_table.created_at ' . $direction);
    }
}
