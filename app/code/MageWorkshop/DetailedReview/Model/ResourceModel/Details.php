<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\ResourceModel;

use MageWorkshop\DetailedReview\Model\Details as DetailsModel;

class Details extends \Magento\Eav\Model\Entity\AbstractEntity
{
    /**
     * @var string $reviewIdField
     */
    protected $reviewIdField = 'review_id';

    /**
     * Getter and lazy loader for _type
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Eav\Model\Entity\Type
     */
    public function getEntityType()
    {
        if (null === $this->_type) {
            $this->setType(DetailsModel::ENTITY);
        }
        return parent::getEntityType();
    }

    /**
     * @param DetailsModel $object
     * @param int $reviewId
     * @return $this
     */
    public function loadByReviewId(DetailsModel $object, $reviewId)
    {
        \Magento\Framework\Profiler::start('EAV:load_entity');

        // this method doesn't exist in magento 2.1, so the exception is raised
        //$object->beforeLoad($reviewId);
        $select = $this->_getLoadRowByReviewIdSelect($object, $reviewId);
        $row = $this->getConnection()->fetchRow($select);

        if (is_array($row)) {
            $object->addData($row);
            $this->loadAllAttributes($object);

            $this->_loadModelAttributes($object);
            $this->_afterLoad($object);
            $object->afterLoad();
            $object->setOrigData();
            $object->setHasDataChanges(false);
        } else {
            $object->isObjectNew(true);
        }

        \Magento\Framework\Profiler::stop('EAV:load_entity');
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewIdField()
    {
        return $this->reviewIdField;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   \Magento\Framework\DataObject $object
     * @param   string|int $rowId
     * @return  \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadRowByReviewIdSelect($object, $rowId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getEntityTable()
        )->where(
            $this->getReviewIdField() . ' =?',
            $rowId
        );

        return $select;
    }
}
