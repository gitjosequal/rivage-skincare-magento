<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Wishlist\Model\Item\Option;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory;
use Magento\Catalog\Model\Product\Exception as ProductException;

/**
 * Class Rewards
 *
 * @method \Amasty\Rewards\Model\ResourceModel\History getResource()
 * @method \Amasty\Rewards\Model\ResourceModel\History _getResource()
 */
class History extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\ResourceModel\History');
    }

    public function loadByCustomer($customerId, $action)
    {
        $this->addData($this->getResource()->loadByCustomer($customerId, $action));
        return $this;
    }

    public function saveInHistory($customerId, $actionId)
    {
        $this->addData([
            'customer_id' => $customerId,
            'action_id'      => $actionId
        ]);

        $this->save();
    }

    public function getAppliedActionsId($customerId)
    {
        $actions = $this->getResource()->getAppliedActions($customerId);
        $appliedActions = [];
        foreach ($actions as $action) {
            $appliedActions[$action['action_id']] =+ 1;
        }

        return $appliedActions;
    }

    /**
     * @param int $customerId
     * @param int $startDate
     *
     * @return array
     */
    public function getLastYearActionsId($customerId, $startDate)
    {
        $actions = $this->getResource()->getLastYearActions($customerId, $startDate);
        $appliedActions = [];
        foreach ($actions as $action) {
            $appliedActions[$action['action_id']] =+ 1;
        }

        return $appliedActions;
    }
}
