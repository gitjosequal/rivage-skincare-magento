<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Plugin\Review\Model\ResourceModel\Review;

use Magento\Review\Model\ResourceModel\Review\Collection;
use Magento\Framework\Db\Select;
use MageWorkshop\AdminResponse\Model\AdminResponse;

class CollectionPlugin
{
    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    public function __construct(\MageWorkshop\DetailedReview\Helper\Review $reviewHelper)
    {
        $this->reviewHelper = $reviewHelper;
    }

    /**
     * @param Collection $subject
     * @param Select $result
     * @return mixed
     * @throws \Zend_Db_Select_Exception
     */
    public function afterGetSelect(Collection $subject, Select $result)
    {
        $resource = $subject->getResource();

        if (($fromPart = (array) $result->getPart(Select::FROM))
            && !isset($fromPart[AdminResponse::TABLE_ALIAS])
        ) {
            $adminResponseEntityId = $this->reviewHelper->getReviewEntityIdByCode(AdminResponse::REVIEW_ENTITY_CODE);
            $adminResponseSubSelect = $resource->getConnection()
                ->select()
                ->from(
                    ['admin_response_main_table' => $resource->getTable('review')],
                    ['admin_response_parent_id' => 'admin_response_main_table.entity_pk_value']
                )->join(
                    ['admin_response_detail' => $resource->getTable('review_detail')],
                    'admin_response_main_table.review_id = admin_response_detail.review_id',
                    [AdminResponse::FIELD_NAME => 'admin_response_detail.detail']
                )->where('admin_response_main_table.entity_id = ?', $adminResponseEntityId);

            $result->joinLeft(
                [AdminResponse::TABLE_ALIAS => $adminResponseSubSelect],
                AdminResponse::TABLE_ALIAS . '.admin_response_parent_id = main_table.review_id',
                [AdminResponse::FIELD_NAME]
            );
        }

        return $result;
    }
}
