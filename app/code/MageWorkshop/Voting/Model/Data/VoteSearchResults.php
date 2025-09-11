<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Model\Data;

use MageWorkshop\Voting\Model\Exception\NotFoundException;
use MageWorkshop\Voting\Model\Vote as VoteModel;

class VoteSearchResults implements \MageWorkshop\Voting\Api\Data\VoteSearchResultsInterface
{
    const EXCEPTION_NO_FIRST_VOTE_ITEM = 'Error getting first vote item from the empty result object';

    /** @var VoteModel[] */
    protected $items = [];

    /**
     * Get vote items list.
     *
     * @return VoteModel[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set set vote items list.
     *
     * @param VoteModel[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return VoteModel
     * @throws NotFoundException
     */
    public function getFirstItem()
    {
        if (!empty($this->items)) {
            return array_values($this->items)[0];
        } else {
            throw new NotFoundException(self::EXCEPTION_NO_FIRST_VOTE_ITEM);
        }
    }
}