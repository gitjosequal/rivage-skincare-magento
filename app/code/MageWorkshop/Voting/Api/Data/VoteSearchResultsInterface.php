<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Api\Data;

interface VoteSearchResultsInterface
{
    /**
     * Get vote items list.
     *
     * @return \MageWorkshop\Voting\Model\Vote[]
     */
    public function getItems();

    /**
     * Set set vote items list.
     *
     * @param \MageWorkshop\Voting\Model\Vote[] $items
     * @return $this
     */
    public function setItems(array $items);
}