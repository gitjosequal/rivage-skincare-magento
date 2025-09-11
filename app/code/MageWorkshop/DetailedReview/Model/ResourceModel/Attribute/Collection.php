<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\ResourceModel\Attribute;

use MageWorkshop\DetailedReview\Model\Details;
use MageWorkshop\DetailedReview\Model\Attribute as AttributeModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;

class Collection extends \Magento\Eav\Model\ResourceModel\Attribute\Collection
{
    protected function _construct()
    {
        $this->_init(AttributeModel::class, AttributeResourceModel::class);
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return Details::ENTITY;
    }

    /**
     * @param int $setId
     * @return self
     */
    public function addAttributeSetInfo($setId)
    {
        $this->getSelect()
            ->joinLeft(
                ['eav_entity_attribute' => $this->getTable('eav_entity_attribute')],
                'main_table.attribute_id = eav_entity_attribute.attribute_id '
                    . 'AND eav_entity_attribute.attribute_set_id = ' . (int) $setId,
                ['entity_attribute_id', 'attribute_set_id', 'sort_order']
            );

        return $this;
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        // we can not use empty table for some reason (
        return '';
    }

    /**
     * Initialize collection select
     * We do not want to deal with the scopes, so, unfortunately, need to overwrite this method (
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $select = $this->getSelect();
        $connection = $this->getConnection();
        $entityType = $this->getEntityType();
        $extraTable = $entityType->getAdditionalAttributeTable();
        $mainDescribe = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns = [];

        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }

        $select->from(['main_table' => $this->getResource()->getMainTable()], $mainColumns);

        // additional attribute data table
        $extraDescribe = $connection->describeTable($this->getTable($extraTable));
        $extraColumns = [];
        foreach (array_keys($extraDescribe) as $columnName) {
            if (isset($mainColumns[$columnName])) {
                continue;
            }
            $extraColumns[$columnName] = $columnName;
        }

        $this->addBindParam('mt_entity_type_id', (int)$entityType->getId());
        $select->join(
            ['additional_table' => $this->getTable($extraTable)],
            'additional_table.attribute_id = main_table.attribute_id',
            $extraColumns
        )->where(
            'main_table.entity_type_id = :mt_entity_type_id'
        );

        // scope values

        /*
        <<< START CHANGES
        $scopeDescribe = $connection->describeTable($this->_getEavWebsiteTable());
        unset($scopeDescribe['attribute_id']);
        $scopeColumns = [];
        foreach (array_keys($scopeDescribe) as $columnName) {
            if ($columnName == 'website_id') {
                $scopeColumns['scope_website_id'] = $columnName;
            } else {
                if (isset($mainColumns[$columnName])) {
                    $alias = 'scope_' . $columnName;
                    $condition = 'main_table.' . $columnName . ' IS NULL';
                    $true = 'scope_table.' . $columnName;
                    $false = 'main_table.' . $columnName;
                    $expression = $connection->getCheckSql($condition, $true, $false);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                } elseif (isset($extraColumns[$columnName])) {
                    $alias = 'scope_' . $columnName;
                    $condition = 'additional_table.' . $columnName . ' IS NULL';
                    $true = 'scope_table.' . $columnName;
                    $false = 'additional_table.' . $columnName;
                    $expression = $connection->getCheckSql($condition, $true, $false);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                }
            }
        }

        $select->joinLeft(
            ['scope_table' => $this->_getEavWebsiteTable()],
            'scope_table.attribute_id = main_table.attribute_id AND scope_table.website_id = :scope_website_id',
            $scopeColumns
        );
        $websiteId = $this->getWebsite() ? (int)$this->getWebsite()->getId() : 0;
        $this->addBindParam('scope_website_id', $websiteId);
        >>> END CHANGES
        */
        return $this;
    }
}
