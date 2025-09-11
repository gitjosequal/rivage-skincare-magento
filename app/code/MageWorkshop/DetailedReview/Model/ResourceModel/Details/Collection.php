<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\ResourceModel\Details;

use MageWorkshop\DetailedReview\Model\Details as DetailsModel;
use MageWorkshop\DetailedReview\Model\ResourceModel\Details as DetailsResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Select;
use Magento\Eav\Model\Entity as EavEntity;

class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(DetailsModel::class, DetailsResourceModel::class);
    }

    public function addReviewStatusFilter($reviewStatus = 0)
    {
        $this->getSelect()
            ->join(
                ['status' => $this->getTable('review')],
                'e.review_id = status.review_id',
                []
            )
            ->where('status.status_id = ?', $reviewStatus);
        return $this;
    }

    /**
     * TODO Fixed problem with the select of primary key of the entity for Magento 2.1.1 & 2.1.2
     *
     * Retrieve attributes load select
     *
     * @param string $table
     * @param string[] $attributeIds
     * @return \Magento\Framework\DB\Select
     * @throws LocalizedException
     */
    public function _getLoadAttributesSelect($table, $attributeIds = [])
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $entity = $this->getEntity();
        $linkField = method_exists($entity, 'getLinkField')
            ? $entity->getLinkField()
            : $entity->getEntityIdField();
        $select = $this->getConnection()->select()
            ->from(
                ['e' => $this->getEntity()->getEntityTable()],
                [$linkField]
            )
            ->join(
                ['t_d' => $table],
                "e.{$linkField} = t_d.{$linkField}",
                ['t_d.attribute_id']
            )->where(
                " e.{$linkField} IN (?)",
                array_keys($this->_itemsById)
            )->where(
                't_d.attribute_id IN (?)',
                $attributeIds
            );

        if (($entity->getEntityTable() === EavEntity::DEFAULT_ENTITY_TABLE) && $entity->getTypeId()) {
            $select->where(
                'entity_type_id =?',
                $entity->getTypeId()
            );
        }
        return $select;
    }

    /**
     * Add select values
     *
     * @param Select $select
     * @param string $table
     * @param string $type
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $select->columns(['value' => 't_d.value']);
        return $select;
    }
}
