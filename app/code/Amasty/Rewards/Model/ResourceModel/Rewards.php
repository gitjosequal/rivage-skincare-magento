<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model\ResourceModel;

class Rewards extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_rewards_rewards', 'id');
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();

        $rewardTable = $this->getTable('amasty_rewards_rewards');

        $select = $connection->select()
            ->from(
                $rewardTable,
                'SUM(amount)'
            )
            ->where('customer_id = :customer_id');

        $pointsLeft = $connection->fetchOne(
            $select,
            [
                ':customer_id' => $object->getCustomerId()
            ]
        );

        $this->getConnection()->update(
            $this->getTable('amasty_rewards_rewards'),
            ['points_left' => $pointsLeft],
            ['id = ?' => $object->getId()]
        );

        return parent::_afterSave($object);
    }

    public function loadPointsByCustomerId($customerId)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getMainTable(),
                'SUM(amount)'
            )->where('customer_id=:customer_id');
        $result = $this->getConnection()->fetchOne(
            $select,
            [
                'customer_id' => $customerId
            ]
        );

        if ($result === null) {
            return 0;
        }

        return $result;
    }
}
