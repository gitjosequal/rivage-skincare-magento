<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Plugin\SalesRule\Model;

class Quote
{
    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    protected $_rewardsQuote;

    public function __construct(\Amasty\Rewards\Model\Quote $quote)
    {
        $this->_rewardsQuote = $quote;
    }

    public function afterSave($subject)
    {
        $points = $subject->getData('amrewards_point');

        if ($points) {
            $this->_rewardsQuote->addReward(
                $subject->getEntityId(),
                $subject->getData('amrewards_point')
            );
        }

        return $subject;
    }
}
