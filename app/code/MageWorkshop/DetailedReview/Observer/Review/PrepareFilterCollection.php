<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer\Review;

class PrepareFilterCollection extends AbstractPrepareCollection
{
    const FILTER_REQUEST_PARAM = 'review_filter_by_date';
    
    /** @var \MageWorkshop\DetailedReview\Helper\DateFilterInterval $dateFilterHelper */
    protected $dateFilterHelper;

    /**
     * PrepareFilterCollection constructor.
     * @param \MageWorkshop\DetailedReview\Helper\DateFilterInterval $dateFilterHelper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorkshop\DetailedReview\Helper\DateFilterInterval $dateFilterHelper
    ) {
        parent::__construct($request);
        $this->dateFilterHelper = $dateFilterHelper;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\ResourceModel\Review\Collection $collection */
        $collection = $observer->getEvent()->getData('collection');

        if ($dateInterval = $this->dateFilterHelper->getDateInterval(
            $this->getRequestParam(self::FILTER_REQUEST_PARAM)
        )) {
            $collection->addFieldToFilter('main_table.created_at', ['gteq' => $dateInterval]);
        }
    }
}
