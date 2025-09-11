<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Api;

/**
 * @api
 */
interface VoteRepositoryInterface
{
    /**
     * @param \MageWorkshop\Voting\Model\Vote $vote
     * @return \MageWorkshop\Voting\Model\Vote
     */
    public function save(\MageWorkshop\Voting\Model\Vote $vote);

    /**
     * @param int $id
     * @return \MageWorkshop\Voting\Model\Vote
     */
    public function getById($id);

    /**
     * @param \MageWorkshop\Voting\Model\Vote $vote
     * @return bool
     */
    public function delete(\MageWorkshop\Voting\Model\Vote $vote);

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id);

    /**
     * Get vote objects list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageWorkshop\Voting\Api\Data\VoteSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}