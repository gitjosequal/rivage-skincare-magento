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
 * @package Amasty\Rewards\Model
 */
class Quote extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\ResourceModel\Quote');
    }

    /**
     * @param $quoteId
     * @param $amount
     */
    public function addReward($quoteId, $amount)
    {
        $quote =  $this->getResource()->loadByQuoteId($quoteId);
        if (!$quote) {
            $this->addData([
                'quote_id'      => $quoteId,
                'reward_points' => $amount

            ]);
            $this->save();
        } else {
            $this->addData([
                'id'            => $quote['id'],
                'quote_id'      => $quoteId,
                'reward_points' => $amount

            ]);
            $this->save();
        }
    }
}
