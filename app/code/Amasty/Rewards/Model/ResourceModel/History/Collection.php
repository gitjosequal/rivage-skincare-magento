<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    public $_rewardsDataHelper;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        \Amasty\Rewards\Helper\Data $rewardsDataHelper,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
    ) {
        $this->_rewardsDataHelper = $rewardsDataHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\History', 'Amasty\Rewards\Model\ResourceModel\History');
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFieldToFilter('customer_id', (int)$customerId);
    }

}
