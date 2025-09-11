<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\ResourceModel\Filter;

use Amasty\Affiliate\Api\Data\ProgramInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CustomerIdAndGroupIdProgramFilter
{
    /**
     * @param AbstractCollection $collection
     * @param int $affiliateCustomerId
     * @param int $affiliateCustomerGroupId
     * @param string $tableName
     */
    public function apply(
        AbstractCollection $collection,
        $affiliateCustomerId,
        $affiliateCustomerGroupId,
        $tableName = 'main_table'
    ) {
        $condition = $this->getConditionString((int)$affiliateCustomerId, (int)$affiliateCustomerGroupId, $tableName);
        $collection->getSelect()->where(
            new \Zend_Db_Expr($condition)
        );
    }

    public function getConditionString(
        int $affiliateCustomerId,
        int $affiliateCustomerGroupId,
        string $tableName
    ): string {
        $customerIdsFieldQuoted = "`{$tableName}`.`" . ProgramInterface::AVAILABLE_CUSTOMERS . '`';
        $groupIdsFieldQuoted = "`{$tableName}`.`" . ProgramInterface::AVAILABLE_GROUPS . '`';

        return "
            (
                ({$customerIdsFieldQuoted} IS NULL OR {$customerIdsFieldQuoted} = '')
                AND ({$groupIdsFieldQuoted} IS NULL OR {$groupIdsFieldQuoted} = '')
            ) OR
            (
                FIND_IN_SET('{$affiliateCustomerId}', {$customerIdsFieldQuoted})
                OR FIND_IN_SET('{$affiliateCustomerGroupId}', {$groupIdsFieldQuoted})
            )
        ";
    }
}
