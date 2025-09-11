<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model\ResourceModel;

class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_rewards_history', 'id');
    }

    public function loadByCustomer($customerId, $action)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('customer_id=:customer_id')
            ->where('action=:action');

        $result = $this->getConnection()->fetchRow(
            $select,
            [
                'customer_id' => $customerId,
                'action' => $action
            ]
        );

        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Get all applied actions ID
     *
     * @param int $customerId
     * @return array
     */
    public function getAppliedActions($customerId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('amasty_rewards_history'),
            ['action_id']
        )->where(
            'customer_id=:customer_id'
        );
        return $this->getConnection()
            ->fetchAll(
                $select,
                [
                    'customer_id' => $customerId
                ]
            );
    }

    /**
     * Get Last Year applied actions ID
     *
     * @param int $customerId
     * @param int $startDate
     * @return array
     */
    public function getLastYearActions($customerId, $startDate)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('amasty_rewards_history'),
            ['action_id']
        )->where(
            'customer_id = :customer_id'
        )->where(new \Zend_Db_Expr(
            "DATE_FORMAT(`date`, '%Y-%m-%d') > '".date('Y-m-d', strtotime("-1 year", $startDate))."'"
        ));
        return $this->getConnection()
            ->fetchAll(
                $select,
                ['customer_id' => $customerId]
            );
    }
}
