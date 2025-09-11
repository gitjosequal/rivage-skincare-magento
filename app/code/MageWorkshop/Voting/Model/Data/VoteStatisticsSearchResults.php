<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Model\Data;

use MageWorkshop\Voting\Model\Data\ReviewVotesStatistics;

class VoteStatisticsSearchResults implements \MageWorkshop\Voting\Api\Data\VoteSearchResultsInterface
{
    /**
     * @var ReviewVotesStatistics[]
     */
    protected $items = [];
    /**
     * Get vote items list.
     *
     * @return ReviewVotesStatistics[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set set vote items list.
     *
     * @param ReviewVotesStatistics[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
}